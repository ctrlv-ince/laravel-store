<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Admin
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
        if (!Auth::check()) {
            Log::warning('Admin middleware: User not authenticated');
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
        
        $user = Auth::user();
        
        if (!$user->account) {
            Log::warning('Admin middleware: User has no account record', ['user_id' => $user->user_id]);
            return redirect()->route('dashboard')->with('error', 'Your account is not properly set up. Please contact support.');
        }
        
        if ($user->account->role !== 'admin') {
            Log::warning('Admin middleware: User is not an admin', [
                'user_id' => $user->user_id,
                'role' => $user->account->role
            ]);
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }
        
        Log::info('Admin middleware: Access granted', [
            'user_id' => $user->user_id,
            'path' => $request->path()
        ]);
        
        return $next($request);
    }
} 