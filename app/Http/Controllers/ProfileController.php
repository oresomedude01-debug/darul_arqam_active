<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?? null;
        
        return view('profile.show', compact('user', 'profile'));
    }

    /**
     * Edit user profile form
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? null;
        
        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'phone' => 'nullable|string|max:20',
        ];

        // Only admin can change name
        if ($user->hasRole('Administrator')) {
            $rules['name'] = 'required|string|max:255';
        }

        // Students cannot change email
        if (!$user->hasRole('student')) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $user->id;
        }

        $validated = $request->validate($rules);

        $updateData = ['phone' => null]; // Initialize for profile update

        // Update name only if admin
        if ($user->hasRole('Administrator') && isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        // Update email only if not student
        if (!$user->hasRole('student') && isset($validated['email'])) {
            $user->update(['email' => $validated['email']]);
        }

        // Update or create user_profile
        if (isset($validated['phone'])) {
            $updateData['phone'] = $validated['phone'];
        }
        
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $updateData
        );

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'redirect' => route('profile.show')
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update user password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
                'redirect' => route('profile.show')
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Password changed successfully');
    }
}
