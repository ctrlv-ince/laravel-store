<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Group;
use App\Models\ItemImage;
use App\Models\Inventory;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Item::with(['images', 'groups', 'inventory']);

        // Handle search
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('item_name', 'like', "%{$searchTerm}%")
                  ->orWhere('item_description', 'like', "%{$searchTerm}%");
            });
        }

        // Handle price filtering
        if ($request->has('min_price') && $request->input('min_price') !== null && $request->input('min_price') !== '') {
            $query->where('price', '>=', (float)$request->input('min_price'));
        }

        if ($request->has('max_price') && $request->input('max_price') !== null && $request->input('max_price') !== '') {
            $query->where('price', '<=', (float)$request->input('max_price'));
        }

        // Handle category filtering
        if ($request->has('groups')) {
            $query->whereHas('groups', function($q) use ($request) {
                $q->whereIn('groups.group_id', $request->input('groups'));
            });
        }

        // Get all groups for the filter
        $groups = Group::all();
        
        // Paginate the results
        $items = $query->paginate(12);

        return view('items.index', compact('items', 'groups'));
    }

    /**
     * Display the specified item.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\View\View
     */
    public function show(Item $item)
    {
        $item->load(['images', 'groups', 'inventory']);
        
        // Get related items from the same group
        $relatedItems = collect();
        if ($item->groups->isNotEmpty()) {
            $firstGroup = $item->groups->first();
            $relatedItems = Item::whereHas('groups', function($q) use ($firstGroup) {
                    $q->where('groups.group_id', $firstGroup->group_id);
                })
                ->where('item_id', '!=', $item->item_id)
                ->with('images')
                ->limit(4)
                ->get();
        }
        
        return view('items.show', compact('item', 'relatedItems'));
    }

    /**
     * Show the form for creating a new item.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = Group::all();
        return view('items.create', compact('groups'));
    }

    /**
     * Store a newly created item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,group_id',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create the item
        $item = Item::create([
            'item_name' => $request->item_name,
            'item_description' => $request->item_description,
            'price' => $request->price,
            'date_added' => now(),
        ]);

        // Create inventory record
        $item->inventory()->create([
            'quantity' => $request->quantity
        ]);

        // Attach groups
        $item->groups()->attach($request->groups);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');
                
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'uploaded_at' => now()
                ]);
            }
        }

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\View\View
     */
    public function edit(Item $item)
    {
        $item->load(['images', 'groups', 'inventory']);
        $groups = Group::all();
        return view('items.edit', compact('item', 'groups'));
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,group_id',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update the item
        $item->update([
            'item_name' => $request->item_name,
            'item_description' => $request->item_description,
            'price' => $request->price,
        ]);

        // Update inventory
        if ($item->inventory) {
            $item->inventory->update([
                'quantity' => $request->quantity,
            ]);
        } else {
            $item->inventory()->create([
                'quantity' => $request->quantity
            ]);
        }

        // Update groups
        $item->groups()->sync($request->groups);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');
                
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 && $item->images->isEmpty(),
                    'uploaded_at' => now()
                ]);
            }
        }

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $image_id) {
                $image = ItemImage::find($image_id);
                if ($image && $image->item_id === $item->item_id) {
                    // Delete the file from storage
                    Storage::disk('public')->delete($image->image_path);
                    // Delete the record
                    $image->delete();
                }
            }
        }

        return redirect()->route('items.show', $item)
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Item $item)
    {
        // Delete associated images from storage
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Model relationships with cascading deletes should handle the DB cleanup
        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Excel::import(new ItemsImport, $request->file('file'));
            return redirect()->route('items.index')
                ->with('success', 'Items imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing items: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        $query = Item::query();

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('groups')) {
            $query->whereHas('groups', function($q) use ($request) {
                $q->whereIn('groups.group_id', $request->groups);
            });
        }

        $items = $query->with(['images', 'groups', 'inventory'])->get();

        if ($request->ajax()) {
            return response()->json($items);
        }

        return view('items.index', compact('items'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $items = Item::search($search)->get();
        
        if ($request->ajax()) {
            return response()->json($items);
        }
        
        return view('items.index', compact('items'));
    }
}
