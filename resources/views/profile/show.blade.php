@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-user text-blue-500 mr-2"></i>
    Profile
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Profile Information -->
                    <div class="md:col-span-1">
                        <div class="bg-gray-700 rounded-lg p-6">
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <img class="h-32 w-32 rounded-full object-cover" 
                                         src="{{ Auth::user()->account->profile_image ? asset('storage/' . Auth::user()->account->profile_image) : asset('images/default-avatar.png') }}" 
                                         alt="{{ Auth::user()->first_name }}">
                                    <button type="button" 
                                            class="absolute bottom-0 right-0 bg-blue-500 rounded-full p-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                                <h3 class="mt-4 text-xl font-medium text-white">
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                </h3>
                                <p class="text-sm text-gray-400">
                                    {{ Auth::user()->email }}
                                </p>
                                <div class="mt-4 flex space-x-4">
                                    <a href="#" class="text-gray-400 hover:text-white">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                    <a href="#" class="text-gray-400 hover:text-white">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="text-gray-400 hover:text-white">
                                        <i class="fab fa-github"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="md:col-span-2">
                        <div class="bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-white mb-6">
                                <i class="fas fa-user-cog mr-2"></i>
                                Account Information
                            </h3>
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-400">
                                            First Name
                                        </label>
                                        <input type="text" 
                                               name="first_name" 
                                               id="first_name" 
                                               value="{{ Auth::user()->first_name }}" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-400">
                                            Last Name
                                        </label>
                                        <input type="text" 
                                               name="last_name" 
                                               id="last_name" 
                                               value="{{ Auth::user()->last_name }}" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-400">
                                            Email
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="{{ Auth::user()->email }}" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="phone_number" class="block text-sm font-medium text-gray-400">
                                            Phone Number
                                        </label>
                                        <input type="tel" 
                                               name="phone_number" 
                                               id="phone_number" 
                                               value="{{ Auth::user()->phone_number }}" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="profile_image" class="block text-sm font-medium text-gray-400">
                                            Profile Image
                                        </label>
                                        <input type="file" 
                                               name="profile_image" 
                                               id="profile_image" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-save mr-2"></i>
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password -->
                        <div class="mt-6 bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-white mb-6">
                                <i class="fas fa-key mr-2"></i>
                                Change Password
                            </h3>
                            <form action="{{ route('password.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-400">
                                            Current Password
                                        </label>
                                        <input type="password" 
                                               name="current_password" 
                                               id="current_password" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-400">
                                            New Password
                                        </label>
                                        <input type="password" 
                                               name="new_password" 
                                               id="new_password" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-400">
                                            Confirm New Password
                                        </label>
                                        <input type="password" 
                                               name="new_password_confirmation" 
                                               id="new_password_confirmation" 
                                               class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" 
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-key mr-2"></i>
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 