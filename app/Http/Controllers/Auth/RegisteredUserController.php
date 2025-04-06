<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:accounts,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'age' => ['required', 'integer', 'min:13'],
            'sex' => ['required', 'in:Male,Female'],
            'phone_number' => ['required', 'string', 'max:12', 'unique:users'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $profileImagePath = '';
        if ($request->hasFile('profile_picture')) {
            $profileImagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            
            // Debug logging
            Log::info('Profile picture processed', [
                'path' => $profileImagePath,
                'exists' => Storage::disk('public')->exists($profileImagePath)
            ]);
        } else {
            // Debug logging for when no file is uploaded
            Log::info('No profile picture uploaded', [
                'has_file' => $request->hasFile('profile_picture'),
                'all_files' => $request->allFiles(),
                'input' => $request->all()
            ]);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'sex' => $request->sex,
            'phone_number' => $request->phone_number,
        ]);

        // Create the account record with the profile image
        try {
            $account = Account::create([
                'user_id' => $user->user_id,
                'username' => $request->username, // Use provided username
                'password' => $user->password, // Use the same hashed password
                'role' => 'user',
                'profile_img' => $profileImagePath,
                'account_status' => 'active'
            ]);
            
            Log::info('Account created successfully', [
                'user_id' => $user->user_id,
                'account_id' => $account->account_id,
                'username' => $account->username
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create account', [
                'user_id' => $user->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
} 