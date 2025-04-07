<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Item;
use App\Models\Review;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get all groups with item count
        $groups = Group::withCount('items')->get();

        // Get featured items (latest 4 items with images and inventory)
        $featuredItems = Item::with(['images', 'inventory'])
            ->whereHas('images')
            ->whereHas('inventory', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->latest()
            ->take(4)
            ->get();

        // Get latest reviews with user and item information
        $latestReviews = Review::with(['account.user', 'item'])
            ->orderBy('create_at', 'desc')
            ->take(3)
            ->get();

        return view('welcome', compact('groups', 'featuredItems', 'latestReviews'));
    }
} 