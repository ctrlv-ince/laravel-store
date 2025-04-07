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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Account;

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
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('accounts')->ignore($user->account?->account_id, 'account_id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'phone_number' => ['required', 'string', 'max:20'],
            'age' => ['required', 'integer', 'min:1'],
            'sex' => ['required', 'in:Male,Female,Other'],
            'profile_img' => ['nullable', 'image', 'max:2048'] // 2MB max
        ]);

        try {
            DB::beginTransaction();

            // Create or update account first
            if (!$user->account) {
                $account = Account::create([
                    'username' => $validated['username'],
                    'password' => $user->password,
                    'role' => 'user',
                    'account_status' => 'active',
                    'profile_img' => '' // Initialize with empty string
                ]);
                $user->account()->associate($account);
            } else {
                $user->account->username = $validated['username'];
            }

            // Handle profile image upload
            if ($request->hasFile('profile_img')) {
                // Delete old profile image if it exists
                if ($user->account && $user->account->profile_img) {
                    Storage::disk('public')->delete($user->account->profile_img);
                }

                // Store the new image
                $imagePath = $request->file('profile_img')->store('profile-images', 'public');
                $user->account->profile_img = $imagePath;
            }

            // Save the account changes
            $user->account->save();

            // Update user information
            $userValidated = collect($validated)
                ->except(['username', 'profile_img'])
                ->toArray();
            $user->fill($userValidated);
            $user->save();

            DB::commit();

            return redirect()->route('profile.edit')->with('status', 'profile-updated');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update failed: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'Failed to update profile. Please try again.');
        }
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