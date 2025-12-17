<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gig;
use Illuminate\Http\Request;

class ProfessionalProfileController extends Controller
{
    /**
     * Display professional's public profile
     */
    public function show($username)
    {
        $user = User::where('username', $username)
            ->where('user_type', 3) // Only professionals
            ->with([
                'profile',
                'gigs' => function($query) {
                    $query->where('is_active', true)
                          ->with(['images', 'packages'])
                          ->latest();
                },
                'gigs.subcategory'
            ])
            ->firstOrFail();
            
        // Check if current user is following
        $isFollowing = auth()->check() ? auth()->user()->isFollowing($user) : false;
        
        // Get user's subcategories
        $subcategories = $user->subcategories()->where('is_active', true)->get();
        
        return view('web.professional-profile', compact('user', 'isFollowing', 'subcategories'));
    }
    
    /**
     * Update profile (for authenticated user editing their own profile)
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'about' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'languages' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'wallpaper_image' => 'nullable|image|max:5120', // 5MB
        ]);
        
        $user = auth()->user();
        
        // Update user table fields
        $user->update($request->only(['name', 'surname', 'business_name']));
        
        // Handle profile picture
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile->profile_picture) {
                \Storage::disk('public')->delete($user->profile->profile_picture);
            }
            
            $profilePicturePath = $request->file('profile_picture')->store('profile', 'public');
            $user->profile->profile_picture = $profilePicturePath;
            
            // Also update avatar_url in users table for consistency
            $user->avatar_url = $profilePicturePath;
            $user->save();
        }
        
        // Handle wallpaper image
        if ($request->hasFile('wallpaper_image')) {
            // Delete old wallpaper if exists
            if ($user->profile->wallpaper_image) {
                \Storage::disk('public')->delete($user->profile->wallpaper_image);
            }
            
            $wallpaperPath = $request->file('wallpaper_image')->store('wallpapers', 'public');
            $user->profile->wallpaper_image = $wallpaperPath;
        }
        
        // Update profile fields
        $user->profile->update($request->only([
            'bio',
            'about',
            'phone',
            'city',
            'country',
            'languages'
        ]));
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = auth()->user();
        $user->load('profile', 'subcategories');
        
        return view('web.profile-edit', compact('user'));
    }
    
    /**
     * Preview how profile appears to customers (for professionals)
     */
    public function preview()
    {
        $user = auth()->user();
        
        if (!$user->isProfessional()) {
            return redirect()->route('web.index')->with('error', 'Only professionals can preview their profile');
        }
        
        $user->load([
            'profile',
            'gigs' => function($query) {
                $query->where('is_active', true)
                      ->with(['images', 'packages'])
                      ->latest();
            },
            'gigs.subcategory'
        ]);
        
        $isFollowing = false; // Preview mode, not following
        $subcategories = $user->subcategories()->where('is_active', true)->get();
        
        return view('web.professional-profile', [
            'user' => $user,
            'isFollowing' => $isFollowing,
            'subcategories' => $subcategories,
            'isPreview' => true
        ]);
    }
}
