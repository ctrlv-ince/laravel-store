@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-envelope text-blue-500 text-6xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Verify Your Email Address
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Before proceeding, please check your email for a verification link.
            </p>
        </div>

        @if (session('resent'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">A fresh verification link has been sent to your email address.</span>
            </div>
        @endif

        <div class="text-center">
            <p class="text-sm text-gray-400">
                If you did not receive the email,
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="font-medium text-blue-500 hover:text-blue-400">
                        click here to request another
                    </button>
                </form>
            </p>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-400">
                Already verified?
                <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:text-blue-400">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
