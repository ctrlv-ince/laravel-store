@extends('layouts.app')

@section('header')
<div class="flex items-center">
    <i class="fas fa-credit-card text-green-500 mr-2"></i>
    <span>Checkout</span>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('error'))
        <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
        @endif
        
        @if(!empty($unavailableItems))
        <div class="bg-yellow-600 text-white p-4 rounded-lg mb-6">
            <h3 class="font-bold text-lg mb-2">Some items in your cart are unavailable or have insufficient stock:</h3>
            <ul class="list-disc list-inside">
                @foreach($unavailableItems as $item)
                <li>{{ $item['item_name'] }} - Requested: {{ $item['requested_quantity'] }}, Available: {{ $item['available_quantity'] }}</li>
                @endforeach
            </ul>
            <div class="mt-3">
                <a href="{{ route('cart.index') }}" class="underline">Return to cart to update quantities</a>
            </div>
        </div>
        @endif
        
        <div class="bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-white mb-6">Order Summary</h2>
                
                <div class="space-y-4 mb-6">
                    @foreach($cartItems as $item)
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-700 rounded overflow-hidden">
                            @if($item->item->images->isNotEmpty())
                                <img class="w-full h-full object-cover" src="{{ asset('storage/' . $item->item->images->first()->image_path) }}" alt="{{ $item->item->item_name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-600">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-white font-medium">{{ $item->item->item_name }}</h3>
                            <p class="text-gray-400 text-sm">{{ $item->quantity }} × ₱{{ number_format($item->item->price, 2) }}</p>
                        </div>
                        <div class="text-white font-bold">
                            ₱{{ number_format($item->item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-700 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Subtotal</span>
                        <span class="text-white">₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Shipping</span>
                        <span class="text-white">Free</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-700 pt-2 mt-2">
                        <span class="text-white font-bold">Total</span>
                        <span class="text-white font-bold text-xl">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <div class="mt-8">
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <div class="flex items-center mb-6">
                            <input type="checkbox" id="terms" name="terms" class="text-blue-500 focus:ring-blue-500 h-4 w-4 bg-gray-700" required>
                            <label for="terms" class="ml-3 text-gray-300">
                                I agree to the <a href="#" class="text-blue-500 hover:underline">Terms and Conditions</a> and <a href="#" class="text-blue-500 hover:underline">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route('cart.index') }}" class="inline-block text-gray-300 hover:text-white">
                                <i class="fas fa-arrow-left mr-2"></i> Return to Cart
                            </a>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-md font-medium">
                                <i class="fas fa-check-circle mr-2"></i> Complete Order - ₱{{ number_format($total, 2) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection