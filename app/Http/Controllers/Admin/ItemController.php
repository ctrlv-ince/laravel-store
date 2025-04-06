<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Group;
use App\Models\Inventory;
use App\Models\ItemImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::with(['groups', 'inventory', 'images'])->get();
            
            return DataTables::of($items)
                ->addColumn('image', function ($item) {
                    $image = $item->images->first();
                    if ($image) {
                        return '<img src="' . asset('storage/' . $image->image_path) . '" alt="' . $item->item_name . '" class="h-12 w-12 object-cover rounded-lg">';
                    }
                    return '<div class="h-12 w-12 bg-gray-700 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-500"></i></div>';
                })
                ->addColumn('category', function ($item) {
                    $group = $item->groups->first(); // Get the first group since it's a many-to-many relationship
                    return $group ? $group->group_name : 'Uncategorized';
                })
                ->addColumn('price', function ($item) {
                    return $item->price;
                })
                ->addColumn('stock', function ($item) {
                    $qty = $item->inventory ? $item->inventory->quantity : 0;
                    $badge = 'bg-green-500';
                    if ($qty <= 0) {
                        $badge = 'bg-red-500';
                    } elseif ($qty < 5) {
                        $badge = 'bg-yellow-500';
                    }
                    return '<span class="px-2 py-1 text-xs rounded-full ' . $badge . '">' . $qty . ' units</span>';
                })
                ->addColumn('action', function ($item) {
                    $viewBtn = '<a href="' . route('admin.items.show', $item->item_id) . '" class="bg-blue-500 hover:bg-blue-600 text-white text-xs p-1 rounded mr-1">
                                <i class="fas fa-eye"></i> View
                            </a>';
                    $editBtn = '<a href="' . route('admin.items.edit', $item->item_id) . '" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs p-1 rounded mr-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>';
                    $deleteBtn = '<button data-id="' . $item->item_id . '" class="bg-red-500 hover:bg-red-600 text-white text-xs p-1 rounded delete-btn">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $viewBtn . $editBtn . $deleteBtn;
                })
                ->rawColumns(['image', 'stock', 'action'])
                ->make(true);
        }
        
        return view('admin.items.index');
    }

    /**
     * Show the form for creating a new item.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = Group::all();
        return view('admin.items.create', compact('groups'));
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
            'group_id' => 'nullable|exists:groups,group_id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'item_description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create item
            $item = Item::create([
                'item_name' => $request->item_name,
                'price' => $request->price,
                'item_description' => $request->item_description,
            ]);
            
            // Attach to group if provided
            if ($request->group_id) {
                $item->groups()->attach($request->group_id);
            }
            
            // Create inventory
            Inventory::create([
                'item_id' => $item->item_id,
                'quantity' => $request->quantity
            ]);
            
            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('items', 'public');
                    
                    ItemImage::create([
                        'item_id' => $item->item_id,
                        'image_path' => $path
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.items.index')
                ->with('success', 'Product created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating item: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating the product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $item = Item::with(['groups', 'inventory', 'images', 'reviews.account.user'])->findOrFail($id);
        
        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $item = Item::with(['groups', 'inventory', 'images'])->findOrFail($id);
        $groups = Group::all();
        
        return view('admin.items.edit', compact('item', 'groups'));
    }

    /**
     * Update the specified item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'group_id' => 'nullable|exists:groups,group_id',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            DB::beginTransaction();
            
            $item = Item::findOrFail($id);
            
            // Update item
            $item->update([
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'price' => $request->price
            ]);
            
            // Update group (detach all and attach new if provided)
            $item->groups()->detach();
            if ($request->group_id) {
                $item->groups()->attach($request->group_id);
            }
            
            // Update inventory
            if ($item->inventory) {
                $item->inventory->update([
                    'quantity' => $request->quantity
                ]);
            } else {
                Inventory::create([
                    'item_id' => $item->item_id,
                    'quantity' => $request->quantity
                ]);
            }
            
            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('items', 'public');
                    
                    ItemImage::create([
                        'item_id' => $item->item_id,
                        'image_path' => $path
                    ]);
                }
            }
            
            // Handle deleted images
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = ItemImage::find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.items.index')
                ->with('success', 'Product updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating item: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating the product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $item = Item::with('images')->findOrFail($id);

            // Delete images from storage
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // The item, images, and inventory will be deleted due to cascade delete in the database
            $item->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully.'
                ]);
            }

            return redirect()->route('admin.items.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete product: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.items.index')
                ->with('error', 'Failed to delete product. ' . $e->getMessage());
        }
    }

    /**
     * Import products from Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);
        
        try {
            Excel::import(new ProductsImport, $request->file('import_file'));
            
            return redirect()->route('admin.items.index')
                ->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to import products: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to import products: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display a listing of the trashed items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $trashedItems = Item::onlyTrashed()
                ->with(['groups', 'inventory', 'images'])
                ->get();
            
            return DataTables::of($trashedItems)
                ->addColumn('image', function ($item) {
                    $image = $item->images->first();
                    if ($image) {
                        return '<img src="' . asset('storage/' . $image->image_path) . '" alt="' . $item->item_name . '" class="h-12 w-12 object-cover rounded-lg">';
                    }
                    return '<div class="h-12 w-12 bg-gray-700 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-500"></i></div>';
                })
                ->addColumn('category', function ($item) {
                    $group = $item->groups->first();
                    return $group ? $group->group_name : 'Uncategorized';
                })
                ->addColumn('deleted_at', function ($item) {
                    return $item->deleted_at->format('M d, Y H:i');
                })
                ->addColumn('action', function ($item) {
                    $restoreBtn = '<button data-id="' . $item->item_id . '" class="bg-green-500 hover:bg-green-600 text-white text-xs p-1 rounded mr-1 restore-btn">
                                <i class="fas fa-trash-restore"></i> Restore
                            </button>';
                    $deleteBtn = '<button data-id="' . $item->item_id . '" class="bg-red-500 hover:bg-red-600 text-white text-xs p-1 rounded force-delete-btn">
                                <i class="fas fa-trash"></i> Delete Permanently
                            </button>';
                    return $restoreBtn . $deleteBtn;
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }
        
        // For non-AJAX requests, count trashed items for debugging
        $trashedCount = Item::onlyTrashed()->count();
        
        return view('admin.items.trash', [
            'trashedCount' => $trashedCount
        ]);
    }

    /**
     * Restore the specified trashed item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        try {
            $item = Item::onlyTrashed()->findOrFail($id);
            $item->restore();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product restored successfully.'
                ]);
            }
            
            return redirect()->route('admin.items.trash')
                ->with('success', 'Product restored successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to restore product: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore product. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.items.trash')
                ->with('error', 'Failed to restore product. ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified trashed item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            $item = Item::onlyTrashed()->with('images')->findOrFail($id);
            
            // Delete images from storage
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            $item->forceDelete();
            
            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product permanently deleted.'
                ]);
            }
            
            return redirect()->route('admin.items.trash')
                ->with('success', 'Product permanently deleted.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to permanently delete product: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to permanently delete product. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.items.trash')
                ->with('error', 'Failed to permanently delete product. ' . $e->getMessage());
        }
    }
} 