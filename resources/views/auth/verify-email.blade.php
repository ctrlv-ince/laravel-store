@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-envelope-open-text text-blue-500 text-6xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Verify your email
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                We need to verify your email address before you can continue
            </p>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 text-gray-300 text-sm">
            <p class="mb-4">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
            </p>
            <p>
                If you didn't receive the email, we will gladly send you another.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-blue-900 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>A new verification link has been sent to the email address you provided during registration.</span>
                </div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" 
                        class="w-full sm:w-auto flex justify-center items-center py-2 px-6 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" 
                        class="w-full sm:w-auto flex justify-center items-center py-2 px-6 border border-gray-700 text-sm font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Log Out
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-400">
                Need help? <a href="#" class="font-medium text-blue-500 hover:text-blue-400">Contact Support</a>
            </p>
        </div>
    </div>
</div>
@endsection 