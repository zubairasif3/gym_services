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
            'username' => 'required|string|max:255|unique:users,username,' . auth()->id(),
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'business_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:160',
            'about' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'languages' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
            'wallpaper_image' => 'nullable|image|max:5120', // 5MB
        ]);
        
        $user = auth()->user();
        
        // Ensure profile exists
        if (!$user->profile) {
            $user->profile()->create(['user_id' => $user->id]);
            $user->refresh();
        }
        
        // Update user table fields
        $user->update($request->only(['name', 'surname', 'username', 'email', 'business_name']));
        
        // Ensure public storage directories exist (Windows symlink workaround)
        $publicStoragePath = public_path('storage');
        if (!is_dir($publicStoragePath . '/profiles/avatars')) {
            \File::makeDirectory($publicStoragePath . '/profiles/avatars', 0755, true);
        }
        if (!is_dir($publicStoragePath . '/profiles/wallpapers')) {
            \File::makeDirectory($publicStoragePath . '/profiles/wallpapers', 0755, true);
        }
        
        // Handle avatar/profile picture (form uses 'avatar' but we store as 'profile_picture')
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Delete old picture if exists
            if ($user->profile->profile_picture) {
                \Storage::disk('public')->delete($user->profile->profile_picture);
                // Also delete from public storage if exists
                $oldPublicPath = $publicStoragePath . '/' . $user->profile->profile_picture;
                if (file_exists($oldPublicPath)) {
                    @unlink($oldPublicPath);
                }
            }
            if ($user->avatar_url) {
                \Storage::disk('public')->delete($user->avatar_url);
                $oldPublicPath = $publicStoragePath . '/' . $user->avatar_url;
                if (file_exists($oldPublicPath)) {
                    @unlink($oldPublicPath);
                }
            }
            
            $avatarPath = $request->file('avatar')->store('profiles/avatars', 'public');
            
            // Copy to public storage for immediate access (Windows symlink workaround)
            $sourcePath = storage_path('app/public/' . $avatarPath);
            $destPath = $publicStoragePath . '/' . $avatarPath;
            if (file_exists($sourcePath) && is_dir(dirname($destPath))) {
                @copy($sourcePath, $destPath);
            }
            
            // Also update avatar_url in users table for consistency
            $user->avatar_url = $avatarPath;
            $user->save();
        }
        
        // Handle wallpaper image
        $wallpaperPath = null;
        if ($request->hasFile('wallpaper_image')) {
            // Delete old wallpaper if exists
            if ($user->profile->wallpaper_image) {
                \Storage::disk('public')->delete($user->profile->wallpaper_image);
                $oldPublicPath = $publicStoragePath . '/' . $user->profile->wallpaper_image;
                if (file_exists($oldPublicPath)) {
                    @unlink($oldPublicPath);
                }
            }
            
            $wallpaperPath = $request->file('wallpaper_image')->store('profiles/wallpapers', 'public');
            
            // Copy to public storage for immediate access (Windows symlink workaround)
            $sourcePath = storage_path('app/public/' . $wallpaperPath);
            $destPath = $publicStoragePath . '/' . $wallpaperPath;
            if (file_exists($sourcePath) && is_dir(dirname($destPath))) {
                @copy($sourcePath, $destPath);
            }
        }
        
        // Process skills - convert comma-separated string to JSON array
        $skillsData = null;
        if ($request->filled('skills')) {
            $skillsArray = array_map('trim', explode(',', $request->skills));
            $skillsArray = array_filter($skillsArray); // Remove empty values
            $skillsData = !empty($skillsArray) ? json_encode($skillsArray) : null;
        }
        
        // Update profile fields
        $profileData = $request->only([
            'bio',
            'about',
            'phone',
            'city',
            'country',
            'languages'
        ]);
        
        // Add image paths to profile data if they were uploaded
        if ($avatarPath !== null) {
            $profileData['profile_picture'] = $avatarPath;
        }
        
        if ($wallpaperPath !== null) {
            $profileData['wallpaper_image'] = $wallpaperPath;
        }
        
        if ($skillsData !== null) {
            $profileData['skills'] = $skillsData;
        }
        
        $user->profile->update($profileData);
        
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
