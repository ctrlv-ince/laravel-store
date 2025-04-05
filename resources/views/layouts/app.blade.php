<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tech Components Store') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-gray-800 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-400">
                                <i class="fas fa-microchip text-2xl"></i>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-500 transition duration-150 ease-in-out">
                                <i class="fas fa-home mr-2"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('items.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-box mr-2"></i>
                                Products
                            </a>
                            <a href="{{ route('orders.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Orders
                            </a>
                            @if(Auth::user()->account->isAdmin())
                            <a href="{{ route('accounts.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-users mr-2"></i>
                                Users
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div>
                                <button type="button" 
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out" 
                                        id="user-menu-button" 
                                        aria-expanded="false" 
                                        aria-haspopup="true">
                                    <img class="h-8 w-8 rounded-full object-cover" 
                                         src="{{ Auth::user()->account->profile_image ? asset('storage/' . Auth::user()->account->profile_image) : asset('images/default-avatar.png') }}" 
                                         alt="{{ Auth::user()->first_name }}">
                                </button>
                            </div>

                            <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-gray-800 ring-1 ring-black ring-opacity-5" 
                                 role="menu" 
                                 aria-orientation="vertical" 
                                 aria-labelledby="user-menu-button" 
                                 tabindex="-1">
                                <a href="{{ route('profile.show') }}" 
                                   class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700" 
                                   role="menuitem">
                                    <i class="fas fa-user mr-2"></i>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700" 
                                            role="menuitem">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button type="button" 
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out" 
                                aria-controls="mobile-menu" 
                                aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="hidden sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 border-blue-500 text-base font-medium text-white bg-blue-500/10 focus:outline-none focus:text-white focus:bg-blue-500/10 focus:border-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('items.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-box mr-2"></i>
                        Products
                    </a>
                    <a href="{{ route('orders.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Orders
                    </a>
                    @if(Auth::user()->account->isAdmin())
                    <a href="{{ route('accounts.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-white focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-users mr-2"></i>
                        Users
                    </a>
                    @endif
                </div>

                <div class="pt-4 pb-3 border-t border-gray-700">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full object-cover" 
                                 src="{{ Auth::user()->account->profile_image ? asset('storage/' . Auth::user()->account->profile_image) : asset('images/default-avatar.png') }}" 
                                 alt="{{ Auth::user()->first_name }}">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                            <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('profile.show') }}" 
                           class="block px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-left px-4 py-2 text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    @yield('header')
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        // Mobile menu toggle
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.querySelector('[role="menu"]').classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.querySelector('[role="menu"]');
            const button = document.getElementById('user-menu-button');
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Mobile menu toggle
        document.querySelector('[aria-controls="mobile-menu"]').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
