@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-microchip text-blue-500 text-6xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Join our community of tech enthusiasts
            </p>
        </div>
        <form id="registerForm" class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="first_name" class="sr-only">First Name</label>
                    <input id="first_name" name="first_name" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="First Name" value="{{ old('first_name') }}"
                           minlength="2" maxlength="50">
                    <span class="error-message text-red-500 text-xs hidden" id="first_name_error"></span>
                    @error('first_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="sr-only">Last Name</label>
                    <input id="last_name" name="last_name" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Last Name" value="{{ old('last_name') }}"
                           minlength="2" maxlength="50">
                    <span class="error-message text-red-500 text-xs hidden" id="last_name_error"></span>
                    @error('last_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Username" value="{{ old('username') }}"
                           minlength="3" maxlength="50" pattern="^[a-zA-Z0-9_]+$">
                    <span class="error-message text-red-500 text-xs hidden" id="username_error"></span>
                    @error('username')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="age" class="sr-only">Age</label>
                    <input id="age" name="age" type="number" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Age" value="{{ old('age') }}"
                           min="13" max="120">
                    <span class="error-message text-red-500 text-xs hidden" id="age_error"></span>
                    @error('age')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="sex" class="sr-only">Sex</label>
                    <select id="sex" name="sex" required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                        <option value="">Select Sex</option>
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    <span class="error-message text-red-500 text-xs hidden" id="sex_error"></span>
                    @error('sex')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="phone_number" class="sr-only">Phone Number</label>
                    <input id="phone_number" name="phone_number" type="tel" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Phone Number" value="{{ old('phone_number') }}"
                           pattern="^[0-9+\-\s]{10,12}$">
                    <span class="error-message text-red-500 text-xs hidden" id="phone_number_error"></span>
                    @error('phone_number')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address" value="{{ old('email') }}">
                    <span class="error-message text-red-500 text-xs hidden" id="email_error"></span>
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Password"
                           minlength="8">
                    <span class="error-message text-red-500 text-xs hidden" id="password_error"></span>
                    @error('password')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Confirm Password">
                    <span class="error-message text-red-500 text-xs hidden" id="password_confirmation_error"></span>
                </div>
                <div>
                    <label for="profile_picture" class="sr-only">Profile Picture</label>
                    <input id="profile_picture" name="profile_picture" type="file" accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    <span class="error-message text-red-500 text-xs hidden" id="profile_picture_error"></span>
                    <span class="text-gray-400 text-xs">Allowed file types: JPEG, PNG, JPG, GIF (max 2MB)</span>
                    @error('profile_picture')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Create Account
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-blue-500 hover:text-blue-400">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const allFields = {
            first_name: {
                validate: (value) => {
                    if (!value) return 'First name is required';
                    if (value.length < 2) return 'First name must be at least 2 characters';
                    if (value.length > 50) return 'First name cannot exceed 50 characters';
                    return '';
                }
            },
            last_name: {
                validate: (value) => {
                    if (!value) return 'Last name is required';
                    if (value.length < 2) return 'Last name must be at least 2 characters';
                    if (value.length > 50) return 'Last name cannot exceed 50 characters';
                    return '';
                }
            },
            username: {
                validate: (value) => {
                    if (!value) return 'Username is required';
                    if (value.length < 3) return 'Username must be at least 3 characters';
                    if (value.length > 50) return 'Username cannot exceed 50 characters';
                    if (!/^[a-zA-Z0-9_]+$/.test(value)) return 'Username can only contain letters, numbers, and underscores';
                    return '';
                }
            },
            age: {
                validate: (value) => {
                    if (!value) return 'Age is required';
                    const age = parseInt(value);
                    if (isNaN(age)) return 'Age must be a number';
                    if (age < 13) return 'You must be at least 13 years old';
                    if (age > 120) return 'Please enter a valid age';
                    return '';
                }
            },
            sex: {
                validate: (value) => {
                    if (!value) return 'Sex selection is required';
                    if (!['Male', 'Female'].includes(value)) return 'Please select a valid option';
                    return '';
                }
            },
            phone_number: {
                validate: (value) => {
                    if (!value) return 'Phone number is required';
                    if (!/^[0-9+\-\s]{10,12}$/.test(value)) return 'Please enter a valid phone number (10-12 digits)';
                    return '';
                }
            },
            email: {
                validate: (value) => {
                    if (!value) return 'Email is required';
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) return 'Please enter a valid email address';
                    return '';
                }
            },
            password: {
                validate: (value) => {
                    if (!value) return 'Password is required';
                    if (value.length < 8) return 'Password must be at least 8 characters';
                    return '';
                }
            },
            password_confirmation: {
                validate: (value) => {
                    const password = document.getElementById('password').value;
                    if (!value) return 'Please confirm your password';
                    if (value !== password) return 'Passwords do not match';
                    return '';
                }
            },
            profile_picture: {
                validate: (field) => {
                    if (!field.files || field.files.length === 0) return '';
                    
                    const file = field.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    
                    if (!validTypes.includes(file.type)) {
                        return 'Please upload a valid image file (JPEG, PNG, JPG, GIF)';
                    }
                    
                    if (file.size > 2 * 1024 * 1024) {
                        return 'Image size should not exceed 2MB';
                    }
                    
                    return '';
                }
            }
        };

        // Add input event listeners for real-time validation
        Object.keys(allFields).forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field) return;
            
            field.addEventListener('input', () => {
                validateField(field);
            });
            
            field.addEventListener('blur', () => {
                validateField(field);
            });
        });

        // Validate a single field
        function validateField(field) {
            const fieldName = field.id;
            const errorElement = document.getElementById(`${fieldName}_error`);
            
            if (!allFields[fieldName] || !errorElement) return;
            
            const validator = allFields[fieldName].validate;
            let errorMsg;
            
            if (fieldName === 'profile_picture') {
                errorMsg = validator(field);
            } else {
                errorMsg = validator(field.value);
            }
            
            if (errorMsg) {
                errorElement.textContent = errorMsg;
                errorElement.classList.remove('hidden');
                field.classList.add('border-red-500');
            } else {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
                field.classList.remove('border-red-500');
            }
            
            return !errorMsg;
        }

        // Form submission with validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all fields
            Object.keys(allFields).forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field) return;
                
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Scroll to the first error
                const firstError = document.querySelector('.error-message:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
</script>
@endpush
@endsection
