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
                                <a href="{{ url('/') }}" class="text-blue-500 hover:text-blue-400">
                                    <i class="fas fa-microchip text-2xl"></i>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="{{ url('/') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-white text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fas fa-home mr-2"></i>
                                    Home
                                </a>
                                <a href="{{ route('items.index') }}" 
                                   class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-gray-300 hover:text-white hover:border-gray-300 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fas fa-box mr-2"></i>
                                    Products
                                </a>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            @if (Route::has('login'))
                                <div class="space-x-4">
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-700 hover:bg-gray-600">
                                            <i class="fas fa-tachometer-alt mr-2"></i>
                                            Dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            Log in
                                        </a>
                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-700 hover:bg-gray-600">
                                                <i class="fas fa-user-plus mr-2"></i>
                                                Register
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button type="button" 
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out" 
                                    id="mobile-menu-button"
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
                        <a href="{{ url('/') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-blue-500 text-white bg-blue-500/10 text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                        <a href="{{ route('items.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                            <i class="fas fa-box mr-2"></i>
                            Products
                        </a>
                    </div>
                    @if (Route::has('login'))
                        <div class="pt-4 pb-3 border-t border-gray-700">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative bg-gray-800">
                <!-- Hero image with overlay -->
                <div class="absolute inset-0 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="Tech background" class="w-full h-full object-cover opacity-20">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-800/80 to-gray-900"></div>
                </div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
                    <div class="text-center max-w-3xl mx-auto">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">Premium Tech Components</h1>
                        <p class="text-xl text-gray-300 mb-8">Discover the latest hardware for your next project or upgrade. Quality components, competitive prices, expert support.</p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
                            <a href="{{ route('items.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-store mr-2"></i>
                                Browse Products
                            </a>
                            @guest
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-gray-200 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Account
                            </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="bg-gray-900 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-white">Shop By Category</h2>
                        <p class="mt-2 text-lg text-gray-400">Browse our range of tech components by category</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 md:gap-6">
                        @foreach($groups as $group)
                        <a href="{{ route('items.index', ['groups[]' => $group->group_id]) }}" class="bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 text-center group">
                            <div class="w-12 h-12 mx-auto bg-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                <i class="fas fa-box text-white text-xl"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-white">{{ $group->group_name }}</h3>
                            <p class="mt-1 text-sm text-gray-400">{{ $group->items_count ?? 0 }} items</p>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Featured Products Section -->
            <div class="bg-gray-800 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-white">Featured Products</h2>
                        <p class="mt-2 text-lg text-gray-400">Our most popular tech components</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($featuredItems as $item)
                        <div class="bg-gray-900 overflow-hidden shadow-sm rounded-lg hover:shadow-lg transition-shadow duration-300">
                            <a href="{{ route('items.show', $item) }}">
                                <div class="w-full h-48 bg-gray-800 flex items-center justify-center overflow-hidden">
                                    @if($item->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" 
                                             alt="{{ $item->item_name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-box text-gray-600 text-4xl"></i>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-white font-semibold text-lg mb-2">{{ $item->item_name }}</h3>
                                    <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $item->item_description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-white font-bold">₱{{ number_format($item->price, 2) }}</span>
                                        @if($item->inventory)
                                            <span class="text-sm {{ $item->inventory->quantity > 0 ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $item->inventory->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            <div class="px-4 pb-4 -mt-2">
                                <a href="{{ route('items.show', $item) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm inline-block text-center">
                                    View Product
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('items.index') }}" class="inline-flex items-center justify-center px-5 py-2 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View All Products <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Latest Reviews Section -->
            <div class="bg-gray-900 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-white">Customer Reviews</h2>
                        <p class="mt-2 text-lg text-gray-400">What our customers say about our products</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($latestReviews as $review)
                        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                            <div class="flex items-start mb-4">
                                <div class="flex-shrink-0">
                                    @if($review->account->profile_img)
                                        <img src="{{ asset('storage/' . $review->account->profile_img) }}" 
                                             alt="Reviewer" 
                                             class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                            <span class="text-white text-lg font-semibold">
                                                {{ substr($review->account->user->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-white font-semibold">{{ $review->account->user->first_name }} {{ $review->account->user->last_name }}</h4>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-600' }} text-sm"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-300 text-sm">{{ $review->comment }}</p>
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <a href="{{ route('items.show', $review->item) }}" class="text-sm text-blue-400 hover:text-blue-300 flex items-center">
                                    <span class="line-clamp-1">{{ $review->item->item_name }}</span>
                                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Value Propositions -->
            <div class="bg-gray-900 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                            <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-600 text-white mb-4">
                                <i class="fas fa-shipping-fast text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Fast Shipping</h3>
                            <p class="text-gray-400">Quick delivery on all orders with tracking information provided.</p>
                        </div>
                        
                        <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                            <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-600 text-white mb-4">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Secure Payments</h3>
                            <p class="text-gray-400">Your transactions are secure with our trusted payment systems.</p>
                        </div>
                        
                        <div class="bg-gray-800 p-6 rounded-lg shadow-lg text-center">
                            <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-600 text-white mb-4">
                                <i class="fas fa-headset text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Expert Support</h3>
                            <p class="text-gray-400">Our tech experts are ready to help with your questions and concerns.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-gray-800 border-t border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="md:flex md:justify-between">
                        <div class="mb-8 md:mb-0">
                            <a href="{{ url('/') }}" class="flex items-center">
                                <i class="fas fa-microchip text-blue-500 text-3xl mr-2"></i>
                                <span class="text-white text-xl font-bold">Tech Store</span>
                            </a>
                            <p class="mt-2 text-sm text-gray-400 max-w-md">
                                Your one-stop shop for high-quality tech components and accessories. We offer competitive prices on the latest hardware.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Products</h3>
                                <ul class="mt-4 space-y-2">
                                    <li><a href="#" class="text-gray-400 hover:text-white">Processors</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Graphics Cards</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Memory</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Storage</a></li>
                                </ul>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Support</h3>
                                <ul class="mt-4 space-y-2">
                                    <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">FAQs</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Shipping Info</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Returns</a></li>
                                </ul>
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1">
                                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Legal</h3>
                                <ul class="mt-4 space-y-2">
                                    <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-white">Terms & Conditions</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 border-t border-gray-700 pt-8 flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex space-x-6 md:order-2">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                        <p class="mt-8 md:mt-0 md:order-1 text-sm text-gray-400">
                            &copy; {{ date('Y') }} Tech Store. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Mobile menu toggle
                const mobileMenuButton = document.querySelector('#mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                
                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function () {
                        mobileMenu.classList.toggle('hidden');
                    });
                }
            });
        </script>
    </body>
</html>
