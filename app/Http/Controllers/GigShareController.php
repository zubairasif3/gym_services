<?php

namespace App\Http\Controllers;

use App\Models\Gig;
use App\Models\GigShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GigShareController extends Controller
{
    /**
     * Track a share event
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request, Gig $gig)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:facebook,twitter,whatsapp,linkedin,link',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create share record
        GigShare::create([
            'gig_id' => $gig->id,
            'user_id' => Auth::id(), // Nullable for guests
            'platform' => $request->platform,
            'ip_address' => $request->ip(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Share tracked successfully!',
            'shares_count' => $gig->shares()->count()
        ]);
    }
    
    /**
     * Get share count for a gig
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(Gig $gig)
    {
        $totalShares = $gig->shares()->count();
        $byPlatform = GigShare::getShareCountByPlatform($gig->id);
        
        return response()->json([
            'success' => true,
            'total' => $totalShares,
            'by_platform' => $byPlatform
        ]);
    }
    
    /**
     * Get share statistics
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Gig $gig)
    {
        $shares = $gig->shares()
            ->selectRaw('platform, COUNT(*) as count, DATE(created_at) as date')
            ->groupBy('platform', 'date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $shares
        ]);
    }
    
    /**
     * Generate share URLs for different platforms
     *
     * @param  \App\Models\Gig  $gig
     * @return \Illuminate\Http\JsonResponse
     */
    public function urls(Gig $gig)
    {
        $url = route('gigs.show', $gig->slug);
        $title = $gig->title;
        $description = strip_tags($gig->description);
        
        $urls = [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url),
            'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($url) . "&text=" . urlencode($title),
            'whatsapp' => "https://wa.me/?text=" . urlencode($title . ' ' . $url),
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($url),
            'link' => $url,
        ];
        
        return response()->json([
            'success' => true,
            'urls' => $urls
        ]);
    }
}
