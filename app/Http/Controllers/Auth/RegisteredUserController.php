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
            'first_name' => ['required', 'string', 'min:2', 'max:50'],
            'last_name' => ['required', 'string', 'min:2', 'max:50'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:accounts,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Rules\Password::defaults()],
            'age' => ['required', 'integer', 'min:13', 'max:120'],
            'sex' => ['required', 'in:Male,Female'],
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\-\s]{10,12}$/', 'max:12', 'unique:users'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'first_name.required' => 'First name is required',
            'first_name.min' => 'First name must be at least 2 characters',
            'last_name.required' => 'Last name is required',
            'last_name.min' => 'Last name must be at least 2 characters',
            'username.required' => 'Username is required',
            'username.min' => 'Username must be at least 3 characters',
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
            'age.required' => 'Age is required',
            'age.min' => 'You must be at least 13 years old',
            'age.max' => 'Please enter a valid age',
            'sex.required' => 'Sex selection is required',
            'sex.in' => 'Please select a valid option',
            'phone_number.required' => 'Phone number is required',
            'phone_number.regex' => 'Please enter a valid phone number (10-12 digits)',
            'profile_picture.image' => 'The profile picture must be an image',
            'profile_picture.mimes' => 'The profile picture must be a jpeg, png, jpg or gif file',
            'profile_picture.max' => 'The profile picture size must not exceed 2MB',
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