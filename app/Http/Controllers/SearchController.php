<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Search items with pagination
        $items = Item::search($query)
            ->query(function ($builder) {
                $builder->with(['images', 'inventory', 'groups']);
            })
            ->paginate(12);
            
        return view('search.results', compact('items', 'query'));
    }
} 