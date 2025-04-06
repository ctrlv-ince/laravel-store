@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <a href="{{ route('dashboard') }}" class="mr-4 text-gray-400 hover:text-blue-500 transition-colors duration-150">
        <i class="fas fa-arrow-left"></i>
    </a>
    <i class="fas fa-user text-blue-500 mr-2"></i>
    <span class="mr-2">Profile</span>
    <i class="fas fa-chevron-right text-xs text-gray-600 mx-2"></i>
    <span class="text-gray-400">Edit</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-white">
                    Profile Information
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Update your account's profile information and email address.
                </p>

                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="mb-6">
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                <img class="h-16 w-16 object-cover rounded-full" 
                                     src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/default-avatar.jpg') }}" 
                                     alt="{{ Auth::user()->first_name }}">
                            </div>
                            <div>
                                <label for="profile_picture" class="block text-sm font-medium text-gray-400">
                                    Profile Picture
                                </label>
                                <input id="profile_picture" name="profile_picture" type="file" 
                                       class="mt-1 block w-full text-sm text-white
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-600 file:text-white
                                              hover:file:bg-blue-700
                                              focus:outline-none">
                            </div>
                        </div>
                        @error('profile_picture')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-400">
                            Username
                        </label>
                        <input id="username" name="username" type="text" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('username', Auth::user()->account ? Auth::user()->account->username : Auth::user()->email) }}">
                        @error('username')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-400">
                            First Name
                        </label>
                        <input id="first_name" name="first_name" type="text" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('first_name', Auth::user()->first_name) }}" required>
                        @error('first_name')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-400">
                            Last Name
                        </label>
                        <input id="last_name" name="last_name" type="text" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('last_name', Auth::user()->last_name) }}" required>
                        @error('last_name')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-400">
                            Age
                        </label>
                        <input id="age" name="age" type="number" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('age', Auth::user()->age) }}" required min="13">
                        @error('age')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-400">
                            Sex
                        </label>
                        <select id="sex" name="sex" 
                                class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="Male" {{ old('sex', Auth::user()->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', Auth::user()->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-400">
                            Phone Number
                        </label>
                        <input id="phone_number" name="phone_number" type="text" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('phone_number', Auth::user()->phone_number) }}" required>
                        @error('phone_number')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-400">
                            Email
                        </label>
                        <input id="email" name="email" type="email" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Save
                        </button>

                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-gray-400">
                                Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-white">
                    Update Password
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Ensure your account is using a long, random password to stay secure.
                </p>

                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-400">
                            Current Password
                        </label>
                        <input id="current_password" name="current_password" type="password" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('current_password')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-400">
                            New Password
                        </label>
                        <input id="password" name="password" type="password" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('password')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-400">
                            Confirm Password
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('password_confirmation')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Save
                        </button>

                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-gray-400">
                                Saved.
                            </p>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-red-500">
                    Delete Account
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>

                <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-400">
                            Password
                        </label>
                        <input id="password" name="password" type="password" 
                               class="mt-1 block w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md shadow-sm text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        @error('password', 'userDeletion')
                            <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection