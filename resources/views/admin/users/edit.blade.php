@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-user-edit text-blue-400 mr-2 text-xl"></i>
    Manage User Account
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
        
        <div class="bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-white mb-6 pb-2 border-b border-gray-700">Manage User Account: {{ $user->first_name }} {{ $user->last_name }}</h2>
                
                @if($errors->any())
                <div class="bg-red-600 text-white p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc ml-6 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- User Information (Read-only) -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-white mb-4">User Information <span class="text-sm text-gray-400 ml-2">(read-only)</span></h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-700 p-4 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Name</p>
                            <p class="text-white">{{ $user->first_name }} {{ $user->last_name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Email</p>
                            <p class="text-white">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Phone</p>
                            <p class="text-white">{{ $user->phone_number ?? 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Username</p>
                            <p class="text-white">{{ $user->account->username ?? 'Not set' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Registration Date</p>
                            <p class="text-white">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Last Updated</p>
                            <p class="text-white">{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Account Management (Editable) -->
                <div>
                    <h3 class="text-lg font-medium text-white mb-4">Account Management</h3>
                    
                    <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-400 mb-1">Role</label>
                                <select name="role" id="role" 
                                        class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        {{ $user->user_id == Auth::id() ? 'disabled' : '' }}>
                                    <option value="user" {{ (old('role', $user->account->role ?? '') == 'user') ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ (old('role', $user->account->role ?? '') == 'admin') ? 'selected' : '' }}>Admin</option>
                                </select>
                                @if($user->user_id == Auth::id())
                                <input type="hidden" name="role" value="{{ $user->account->role ?? 'admin' }}">
                                <p class="text-sm text-yellow-500 mt-1">You can't change your own role.</p>
                                @endif
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                                <select name="status" id="status" 
                                        class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        {{ $user->user_id == Auth::id() ? 'disabled' : '' }}>
                                    <option value="active" {{ (old('status', $user->account->account_status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ (old('status', $user->account->account_status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @if($user->user_id == Auth::id())
                                <input type="hidden" name="status" value="{{ $user->account->account_status ?? 'active' }}">
                                <p class="text-sm text-yellow-500 mt-1">You can't deactivate your own account.</p>
                                @else
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                                    Setting a user to inactive will prevent them from logging in to the application.
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-150">
                                Update Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 