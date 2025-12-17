<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\GigReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GigReviewController extends Controller
{
    /**
     * Display a listing of reviews for a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Gig $gig)
    {
        $perPage = request('per_page', 10);
        $sortBy = request('sort_by', 'recent'); // recent, helpful, rating
        
        $query = $gig->reviews()->with('user.profile');
        
        // Apply sorting
        switch ($sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->recent();
        }
        
        // Filter by rating if specified
        if (request('rating')) {
            $query->rating(request('rating'));
        }
        
        $reviews = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $reviews,
            'stats' => [
                'average_rating' => $gig->reviews()->avg('rating'),
                'total_reviews' => $gig->reviews()->count(),
                'rating_breakdown' => $this->getRatingBreakdown($gig),
            ]
        ]);
    }
    
    /**
     * Store a newly created review
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Gig $gig)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to leave a review.'
            ], 401);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if user already reviewed this gig
        $existingReview = GigReview::where('gig_id', $gig->id)
            ->where('user_id', Auth::id())
            ->whereNull('order_id')
            ->first();
        
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this service.'
            ], 422);
        }
        
        // Create the review
        $review = GigReview::create([
            'gig_id' => $gig->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_verified' => false, // Can be set to true if user has purchased
        ]);
        
        // Update gig's average rating
        $this->updateGigRating($gig);
        
        // Load user relationship
        $review->load('user.profile');
        
        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!',
            'data' => $review
        ], 201);
    }
    
    /**
     * Update the specified review
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GigReview  $review
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, GigReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update the review
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        // Update gig's average rating
        $this->updateGigRating($review->gig);
        
        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully!',
            'data' => $review
        ]);
    }
    
    /**
     * Remove the specified review
     *
     * @param  \App\Models\GigReview  $review
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(GigReview $review)
    {
        // Check if user owns this review or is admin
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }
        
        $gig = $review->gig;
        $review->delete();
        
        // Update gig's average rating
        $this->updateGigRating($gig);
        
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully!'
        ]);
    }
    
    /**
     * Mark a review as helpful
     *
     * @param  \App\Models\GigReview  $review
     * @return \Illuminate\Http\JsonResponse
     */
    public function markHelpful(GigReview $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in.'
            ], 401);
        }
        
        // Increment helpful count
        $review->increment('helpful_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'helpful_count' => $review->helpful_count
        ]);
    }
    
    /**
     * Get rating breakdown for a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return array
     */
    private function getRatingBreakdown(Gig $gig)
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $gig->reviews()->where('rating', $i)->count();
        }
        return $breakdown;
    }
    
    /**
     * Update gig's average rating and count
     *
     * @param  \App\Models\Gig  $gig
     * @return void
     */
    private function updateGigRating(Gig $gig)
    {
        $gig->update([
            'rating' => $gig->reviews()->avg('rating'),
            'ratings_count' => $gig->reviews()->count(),
        ]);
    }
}
