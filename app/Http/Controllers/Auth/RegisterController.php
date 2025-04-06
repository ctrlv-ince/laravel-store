<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        register as traitRegister;
    }

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the registration view.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return redirect($this->redirectPath())
            ->with('status', 'Please check your email for a verification link. You must verify your email before logging in.');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:accounts,username'],
            'age' => ['required', 'integer', 'min:18'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'age' => $data['age'],
                'sex' => $data['sex'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $profileImagePath = '';
            if (isset($data['profile_picture'])) {
                $profileImagePath = $data['profile_picture']->store('profile_pictures', 'public');
                
                // Debug logging
                Log::info('Profile picture uploaded via RegisterController', [
                    'user_id' => $user->user_id,
                    'path' => $profileImagePath,
                    'exists' => Storage::disk('public')->exists($profileImagePath)
                ]);
            }

            // Create an account for the user
            try {
                $account = Account::create([
                    'user_id' => $user->user_id,
                    'username' => $data['username'], // Use provided username
                    'password' => $user->password, // Use the same hashed password
                    'role' => 'user',
                    'profile_img' => $profileImagePath,
                    'account_status' => 'active'
                ]);
                
                Log::info('Account created successfully via RegisterController', [
                    'user_id' => $user->user_id,
                    'account_id' => $account->account_id,
                    'username' => $account->username
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create account via RegisterController', [
                    'user_id' => $user->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Re-throw to rollback transaction
            }

            return $user;
        });
    }

    /**
     * Get the path to redirect to.
     *
     * @return string
     */
    public function redirectPath()
    {
        return '/login';
    }
}
