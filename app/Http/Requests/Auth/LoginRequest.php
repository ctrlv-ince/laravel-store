<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Don't automatically merge login as email anymore
        // We'll handle this in authenticate method
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Try to authenticate with username from Account model
        $credentials = ['password' => $this->password];
        $loginField = $this->login;
        
        // First attempt - try by email
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginField;
            if (Auth::attempt($credentials, $this->boolean('remember'))) {
                // Check account status after successful authentication
                $this->checkAccountStatus();
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }
        
        // Second attempt - try with username via Account relation
        $account = \App\Models\Account::where('username', $loginField)->first();
        if ($account) {
            $user = $account->user;
            if ($user && Auth::attempt(['email' => $user->email, 'password' => $this->password], $this->boolean('remember'))) {
                // Check account status after successful authentication
                $this->checkAccountStatus();
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }
        
        // If we reach here, authentication failed
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.failed'),
        ]);
    }
    
    /**
     * Check if authenticated user's account is active.
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    private function checkAccountStatus(): void
    {
        $user = Auth::user();
        
        if ($user->account && $user->account->account_status === 'inactive') {
            Auth::logout();
            
            RateLimiter::hit($this->throttleKey());
            
            throw ValidationException::withMessages([
                'login' => 'Your account has been deactivated. Please contact administrator.',
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}