@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-microchip text-blue-500 text-6xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Enter your email address and we'll send you a link to reset your password
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Send Password Reset Link
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400">
                Remember your password?
                <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:text-blue-400">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
