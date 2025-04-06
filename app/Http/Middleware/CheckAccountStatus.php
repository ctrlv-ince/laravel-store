<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has an account
        if (Auth::check() && Auth::user()->account) {
            // If account status is inactive, log them out
            if (Auth::user()->account->account_status === 'inactive') {
                Auth::logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been deactivated. Please contact administrator.');
            }
        }
        
        return $next($request);
    }
} 