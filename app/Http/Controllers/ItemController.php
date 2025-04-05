<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Group;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::with(['images', 'groups', 'inventory']);
            
            return DataTables::of($items)
                ->addColumn('primary_image', function($item) {
                    $image = $item->images()->where('is_primary', true)->first();
                    return $image ? asset('storage/' . $image->image_path) : null;
                })
                ->addColumn('stock', function($item) {
                    return $item->inventory ? $item->inventory->quantity : 0;
                })
                ->addColumn('groups', function($item) {
                    return $item->groups->pluck('group_name')->implode(', ');
                })
                ->addColumn('action', function($item) {
                    return view('items.actions', compact('item'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $groups = Group::all();
        return view('items.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::all();
        return view('items.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|unique:items|max:100',
            'price' => 'required|numeric|min:0',
            'item_description' => 'required',
            'quantity' => 'required|integer|min:0',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,group_id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item = Item::create([
            'item_name' => $request->item_name,
            'price' => $request->price,
            'item_description' => $request->item_description,
            'date_added' => now()
        ]);

        $item->inventory()->create([
            'quantity' => $request->quantity
        ]);

        $item->groups()->attach($request->groups);

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
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load(['images', 'groups', 'inventory', 'reviews.account.user']);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $item->load(['images', 'groups', 'inventory']);
        $groups = Group::all();
        return view('items.edit', compact('item', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|max:100|unique:items,item_name,' . $item->item_id . ',item_id',
            'price' => 'required|numeric|min:0',
            'item_description' => 'required',
            'quantity' => 'required|integer|min:0',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,group_id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item->update([
            'item_name' => $request->item_name,
            'price' => $request->price,
            'item_description' => $request->item_description
        ]);

        $item->inventory()->update([
            'quantity' => $request->quantity
        ]);

        $item->groups()->sync($request->groups);

        if ($request->hasFile('images')) {
            // Delete old images if replace_images is checked
            if ($request->has('replace_images')) {
                foreach ($item->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $item->images()->delete();
            }

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('items', 'public');
                $item->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 && $request->has('replace_images'),
                    'uploaded_at' => now()
                ]);
            }
        }

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
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
