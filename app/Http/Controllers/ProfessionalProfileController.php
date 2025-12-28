<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gig;
use App\Models\ProfileMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                'activeProfileMedia',
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
        // Restrict to professionals only
        if (!auth()->check() || !auth()->user()->isProfessional()) {
            return redirect()->route('web.index')->with('error', 'Only professionals can edit their profile');
        }
        
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
        // Restrict to professionals only
        if (!auth()->check() || !auth()->user()->isProfessional()) {
            return redirect()->route('web.index')->with('error', 'Only professionals can edit their profile');
        }
        
        $user = auth()->user();
        $user->load('profile', 'subcategories', 'activeProfileMedia');
        
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
            'activeProfileMedia',
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

    /**
     * Upload profile media (images/videos)
     */
    public function uploadMedia(Request $request)
    {
        // Restrict to professionals only
        if (!auth()->check() || !auth()->user()->isProfessional()) {
            return response()->json(['error' => 'Only professionals can upload media'], 403);
        }

        $request->validate([
            'media' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,mov,avi|max:20480', // 20MB max
            'media_type' => 'required|in:image,video',
        ]);

        $user = auth()->user();
        $mediaType = $request->media_type;

        // For videos, validate duration (max 10 seconds)
        if ($mediaType === 'video') {
            // We'll check duration after upload
        }

        // Count existing media
        $existingMediaCount = $user->profileMedia()->count();
        if ($existingMediaCount >= 20) {
            return response()->json(['error' => 'Maximum 20 media items allowed'], 400);
        }

        // Ensure public storage directories exist
        $publicStoragePath = public_path('storage');
        if (!is_dir($publicStoragePath . '/profiles/media')) {
            \File::makeDirectory($publicStoragePath . '/profiles/media', 0755, true);
        }

        // Store the file
        $filePath = $request->file('media')->store('profiles/media', 'public');

        // Copy to public storage (Windows symlink workaround)
        $sourcePath = storage_path('app/public/' . $filePath);
        $destPath = $publicStoragePath . '/' . $filePath;
        if (file_exists($sourcePath) && is_dir(dirname($destPath))) {
            @copy($sourcePath, $destPath);
        }

        $duration = null;
        $thumbnailPath = null;

        // For videos, we'll need to extract duration and create thumbnail
        // This requires FFmpeg or similar tool installed on server
        if ($mediaType === 'video') {
            // Basic duration check - you may want to use FFmpeg for accurate duration
            // For now, we'll store null and let frontend handle validation
            $duration = $request->input('duration'); // Expect from frontend
        }

        // Create media record
        $media = ProfileMedia::create([
            'user_id' => $user->id,
            'media_type' => $mediaType,
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'duration' => $duration,
            'order' => $existingMediaCount, // Add to end
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => asset('storage/' . $filePath),
        ]);
    }

    /**
     * Delete profile media
     */
    public function deleteMedia($id)
    {
        // Restrict to professionals only
        if (!auth()->check() || !auth()->user()->isProfessional()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $media = ProfileMedia::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete file from storage
        if ($media->file_path) {
            Storage::disk('public')->delete($media->file_path);
            // Delete from public storage
            $publicPath = public_path('storage/' . $media->file_path);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }

        // Delete thumbnail if exists
        if ($media->thumbnail_path) {
            Storage::disk('public')->delete($media->thumbnail_path);
            $publicPath = public_path('storage/' . $media->thumbnail_path);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }

        $media->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reorder profile media
     */
    public function reorderMedia(Request $request)
    {
        // Restrict to professionals only
        if (!auth()->check() || !auth()->user()->isProfessional()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'integer|exists:profile_media,id',
        ]);

        $user = auth()->user();

        foreach ($request->media_ids as $index => $mediaId) {
            ProfileMedia::where('id', $mediaId)
                ->where('user_id', $user->id)
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
