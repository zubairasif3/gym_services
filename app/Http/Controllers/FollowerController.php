<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    /**
     * Follow a professional
     */
    public function follow(User $user)
    {
        // Check if user is trying to follow themselves
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        // Check if already following
        if (auth()->user()->isFollowing($user)) {
            return response()->json(['error' => 'Already following this user'], 400);
        }

        // Add follower
        auth()->user()->following()->attach($user->id);
        
        // Update cached count
        $user->increment('followers_count');
        auth()->user()->increment('following_count');
        
        // Create notification for the professional
        Notification::create([
            'user_id' => $user->id,
            'type' => 'new_follower',
            'related_user_id' => auth()->id(),
            'data' => json_encode([
                'follower_name' => auth()->user()->name . ' ' . auth()->user()->surname,
                'message' => 'started following you'
            ])
        ]);
        
        return response()->json([
            'success' => true,
            'followers_count' => $user->fresh()->followers_count,
            'message' => 'Successfully followed'
        ]);
    }
    
    /**
     * Unfollow a professional
     */
    public function unfollow(User $user)
    {
        // Check if actually following
        if (!auth()->user()->isFollowing($user)) {
            return response()->json(['error' => 'Not following this user'], 400);
        }

        // Remove follower
        auth()->user()->following()->detach($user->id);
        
        // Update cached count
        $user->decrement('followers_count');
        auth()->user()->decrement('following_count');
        
        return response()->json([
            'success' => true,
            'followers_count' => $user->fresh()->followers_count,
            'message' => 'Successfully unfollowed'
        ]);
    }
    
    /**
     * Get list of professionals the user is following
     */
    public function following()
    {
        $following = auth()->user()->following()
            ->where('user_type', 3) // Only professionals
            ->with(['profile', 'gigs' => function($query) {
                $query->where('is_active', true)->take(3);
            }])
            ->paginate(12);
            
        return view('web.following', compact('following'));
    }
    
    /**
     * Get followers of a specific user
     */
    public function followers(User $user)
    {
        $followers = $user->followers()
            ->with('profile')
            ->paginate(20);
            
        return view('web.followers', compact('user', 'followers'));
    }
    
    /**
     * API: Get follower/following counts
     */
    public function getCount()
    {
        return response()->json([
            'followers_count' => auth()->user()->followers_count,
            'following_count' => auth()->user()->following_count
        ]);
    }
    
    /**
     * API: Check if following a specific user
     */
    public function checkFollowing(User $user)
    {
        return response()->json([
            'is_following' => auth()->user()->isFollowing($user)
        ]);
    }
}
