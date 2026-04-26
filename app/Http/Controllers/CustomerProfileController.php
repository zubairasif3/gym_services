<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerProfileController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user || (int) $user->user_type !== 2) {
            return redirect()->route('web.index')->with('error', 'Only customers can access this page.');
        }

        $user->load('profile');

        return view('web.customer-profile', compact('user'));
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || (int) $user->user_type !== 2) {
            return redirect()->route('web.index')->with('error', 'Only customers can update profile avatar.');
        }

        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        if (! $user->profile) {
            $user->profile()->create(['user_id' => $user->id, 'is_provider' => false]);
            $user->refresh();
        }

        $publicStoragePath = public_path('storage');
        if (! is_dir($publicStoragePath . '/profiles/avatars')) {
            \File::makeDirectory($publicStoragePath . '/profiles/avatars', 0755, true);
        }

        if ($user->avatar_url) {
            \Storage::disk('public')->delete($user->avatar_url);
            $oldPublicPath = $publicStoragePath . '/' . $user->avatar_url;
            if (file_exists($oldPublicPath)) {
                @unlink($oldPublicPath);
            }
        }

        if ($user->profile->profile_picture) {
            \Storage::disk('public')->delete($user->profile->profile_picture);
            $oldPublicPath = $publicStoragePath . '/' . $user->profile->profile_picture;
            if (file_exists($oldPublicPath)) {
                @unlink($oldPublicPath);
            }
        }

        $avatarPath = $request->file('avatar')->store('profiles/avatars', 'public');

        $sourcePath = storage_path('app/public/' . $avatarPath);
        $destPath = $publicStoragePath . '/' . $avatarPath;
        if (file_exists($sourcePath) && is_dir(dirname($destPath))) {
            @copy($sourcePath, $destPath);
        }

        $user->avatar_url = $avatarPath;
        $user->save();

        $user->profile->update([
            'profile_picture' => $avatarPath,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Profile avatar updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || (int) $user->user_type !== 2) {
            return redirect()->route('web.index')->with('error', 'Only customers can update password.');
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Your current password is incorrect.',
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('customer.profile')->with('success', 'Password updated successfully.');
    }

    public function updateDetails(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || (int) $user->user_type !== 2) {
            return redirect()->route('web.index')->with('error', 'Only customers can update profile details.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'cap' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        if (! $user->profile) {
            $user->profile()->create(['user_id' => $user->id, 'is_provider' => false]);
            $user->refresh();
        }

        $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
        ]);

        $user->profile->update([
            'city' => $request->city,
            'country' => $request->country,
            'cap' => $request->cap,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Profile details updated successfully.');
    }
}

