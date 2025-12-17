<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\GigSave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GigSaveController extends Controller
{
    /**
     * Toggle save status for a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Gig $gig)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to save services.',
                'redirect' => route('web.login')
            ], 401);
        }
        
        $userId = Auth::id();
        
        // Check if already saved
        $existingSave = GigSave::where('gig_id', $gig->id)
            ->where('user_id', $userId)
            ->first();
        
        if ($existingSave) {
            // Unsave
            $existingSave->delete();
            
            return response()->json([
                'success' => true,
                'saved' => false,
                'message' => 'Service removed from saved items.',
                'saves_count' => $gig->saves()->count()
            ]);
        } else {
            // Save
            GigSave::create([
                'gig_id' => $gig->id,
                'user_id' => $userId,
            ]);
            
            return response()->json([
                'success' => true,
                'saved' => true,
                'message' => 'Service saved successfully!',
                'saves_count' => $gig->saves()->count()
            ]);
        }
    }
    
    /**
     * Check if user has saved a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Gig $gig)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'saved' => false
            ]);
        }
        
        $saved = GigSave::where('gig_id', $gig->id)
            ->where('user_id', Auth::id())
            ->exists();
        
        return response()->json([
            'success' => true,
            'saved' => $saved
        ]);
    }
    
    /**
     * Get user's saved gigs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function savedGigs()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }
        
        $perPage = request('per_page', 12);
        
        $savedGigs = Auth::user()
            ->savedGigs()
            ->with(['user.profile', 'images'])
            ->withCount(['reviews', 'saves'])
            ->active()
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $savedGigs
        ]);
    }
    
    /**
     * Get save count for a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(Gig $gig)
    {
        return response()->json([
            'success' => true,
            'count' => $gig->saves()->count()
        ]);
    }
}
