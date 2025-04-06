<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:accounts,username,' . ($user->account ? $user->account->account_id : '0') . ',account_id'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
            'age' => ['required', 'integer', 'min:13'],
            'sex' => ['required', 'in:Male,Female'],
            'phone_number' => ['required', 'string', 'max:12', 'unique:users,phone_number,'.$user->user_id.',user_id'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Make sure the user has an account
            $account = $user->account;
            
            if (!$account) {
                // Create an account if it doesn't exist
                $account = new \App\Models\Account([
                    'user_id' => $user->user_id,
                    'username' => $validated['username'],
                    'password' => $user->password,
                    'role' => 'user',
                    'account_status' => 'active',
                    'profile_img' => ''
                ]);
                $account->save();
                
                Log::info('Created new account for existing user', [
                    'user_id' => $user->user_id,
                    'account_id' => $account->account_id
                ]);
            }
            
            // Delete old profile picture if it exists
            if ($account->profile_img) {
                Storage::disk('public')->delete($account->profile_img);
            }
            
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $account->profile_img = $path;
            $account->save();
            
            // Debug logging
            Log::info('Profile picture updated on account', [
                'user_id' => $user->user_id,
                'account_id' => $account->account_id,
                'path' => $path,
                'exists' => Storage::disk('public')->exists($path)
            ]);
        } else {
            // Debug logging for when no file is uploaded
            Log::info('No profile picture in update', [
                'has_file' => $request->hasFile('profile_picture'),
                'all_files' => $request->allFiles()
            ]);
        }
        
        // Update or create account with username
        $account = $user->account;
        if (!$account) {
            $account = new \App\Models\Account([
                'user_id' => $user->user_id,
                'username' => $validated['username'],
                'password' => $user->password,
                'role' => 'user',
                'account_status' => 'active',
                'profile_img' => ''
            ]);
            $account->save();
        } else {
            $account->username = $validated['username'];
            $account->save();
        }

        // Remove the username from validated data before filling user model
        $userValidated = collect($validated)->except(['username'])->toArray();
        $user->fill($userValidated);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        // Delete profile picture if it exists
        if ($user->account && $user->account->profile_img) {
            Storage::disk('public')->delete($user->account->profile_img);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}