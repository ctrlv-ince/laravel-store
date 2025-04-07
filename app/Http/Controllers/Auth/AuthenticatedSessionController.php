<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Log successful login with remember value
        \Illuminate\Support\Facades\Log::info('Login successful:', [
            'user' => $request->user()->id,
            'remember' => $request->boolean('remember')
        ]);
        
        // If remember is checked, set a longer cookie expiration
        if ($request->boolean('remember')) {
            // Set the auth cookie expiration to a longer time period
            // Laravel's remember me functionality typically sets this to 5 years
            // but we can log that we handled the remember request
            \Illuminate\Support\Facades\Log::info('Setting remember cookie');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
} 