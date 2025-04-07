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
        // Log incoming request data for debugging
        Log::info('Product creation attempt', [
            'request_data' => $request->except(['images']),
            'has_images' => $request->hasFile('images'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'file_upload_max_size' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ]);

        $request->validate([
            'item_name' => 'required|string|max:255',
            'group_id' => 'nullable|exists:groups,group_id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'item_description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'item_name.required' => 'Product name is required',
            'item_name.max' => 'Product name cannot exceed 255 characters',
            'group_id.exists' => 'The selected category does not exist',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be 0 or higher',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a whole number',
            'quantity.min' => 'Quantity must be 0 or higher',
            'item_description.required' => 'Description is required',
            'images.*.image' => 'The file must be an image',
            'images.*.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
            'images.*.max' => 'Image size should not exceed 2MB',
        ]);
        
        try {
            Log::info('Starting transaction for item creation');
            DB::beginTransaction();
            
            // Create item
            $item = Item::create([
                'item_name' => $request->item_name,
                'price' => $request->price,
                'item_description' => $request->item_description,
            ]);
            
            Log::info('Item record created', [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name
            ]);
            
            // Attach to group if provided
            if ($request->group_id) {
                $item->groups()->attach($request->group_id);
                Log::info('Group attached to item', [
                    'item_id' => $item->item_id,
                    'group_id' => $request->group_id
                ]);
            }
            
            // Create inventory
            $inventory = Inventory::create([
                'item_id' => $item->item_id,
                'quantity' => $request->quantity
            ]);
            
            Log::info('Inventory record created', [
                'item_id' => $item->item_id,
                'inventory_id' => $inventory->item_id,
                'quantity' => $inventory->quantity
            ]);
            
            // Handle images
            if ($request->hasFile('images')) {
                $imageCount = count($request->file('images'));
                Log::info('Processing images', ['count' => $imageCount]);
                
                foreach ($request->file('images') as $index => $file) {
                    try {
                        // Verify the file is valid
                        if (!$file->isValid()) {
                            Log::error('Invalid file upload', [
                                'index' => $index,
                                'error' => $file->getErrorMessage()
                            ]);
                            continue;
                        }
                        
                        Log::info('Processing image', [
                            'index' => $index, 
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'error' => $file->getError()
                        ]);
                        
                        // Create directory if it doesn't exist
                        $directory = 'public/items';
                        if (!Storage::exists($directory)) {
                            Storage::makeDirectory($directory);
                            Log::info('Created directory', ['directory' => $directory]);
                        }
                        
                        // Store the file
                        $path = $file->store('items', 'public');
                        Log::info('File stored at path', ['path' => $path]);
                        
                        if (!$path) {
                            Log::error('Failed to store file', [
                                'index' => $index,
                                'original_name' => $file->getClientOriginalName()
                            ]);
                            continue;
                        }
                        
                        // Verify the file exists after storing
                        if (!Storage::disk('public')->exists($path)) {
                            Log::error('File not found after storage', [
                                'path' => $path
                            ]);
                        }
                        
                        $image = ItemImage::create([
                            'item_id' => $item->item_id,
                            'image_path' => $path
                        ]);
                        
                        Log::info('Image stored successfully', [
                            'image_id' => $image->image_id,
                            'path' => $path
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to process image ' . $index, [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } else {
                Log::info('No images were provided');
            }
            
            DB::commit();
            Log::info('Product created successfully', ['item_id' => $item->item_id]);
            
            return redirect()->route('admin.items.index')
                ->with('success', 'Product created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating item', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['images'])
            ]);
            
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
        // Log incoming request data for debugging
        Log::info('Product update attempt', [
            'item_id' => $id,
            'request_data' => $request->except(['images']),
            'has_images' => $request->hasFile('images'),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
        ]);
        
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'group_id' => 'nullable|exists:groups,group_id',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'item_name.required' => 'Product name is required',
            'item_name.max' => 'Product name cannot exceed 255 characters',
            'group_id.exists' => 'The selected category does not exist',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be 0 or higher',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a whole number',
            'quantity.min' => 'Quantity must be 0 or higher',
            'item_description.required' => 'Description is required',
            'images.*.image' => 'The file must be an image',
            'images.*.mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed',
            'images.*.max' => 'Image size should not exceed 2MB',
        ]);
        
        try {
            Log::info('Starting transaction for item update', ['item_id' => $id]);
            DB::beginTransaction();
            
            $item = Item::findOrFail($id);
            Log::info('Found item to update', ['item_id' => $item->item_id, 'name' => $item->item_name]);
            
            // Update item
            $item->update([
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'price' => $request->price
            ]);
            Log::info('Item details updated', ['item_id' => $item->item_id]);
            
            // Update group (detach all and attach new if provided)
            $item->groups()->detach();
            if ($request->group_id) {
                $item->groups()->attach($request->group_id);
                Log::info('Group updated for item', ['item_id' => $item->item_id, 'group_id' => $request->group_id]);
            } else {
                Log::info('No group assigned to item', ['item_id' => $item->item_id]);
            }
            
            // Update inventory
            if ($item->inventory) {
                $item->inventory->update([
                    'quantity' => $request->quantity
                ]);
                Log::info('Inventory updated', ['item_id' => $item->item_id, 'quantity' => $request->quantity]);
            } else {
                $inventory = Inventory::create([
                    'item_id' => $item->item_id,
                    'quantity' => $request->quantity
                ]);
                Log::info('New inventory created', ['item_id' => $item->item_id, 'quantity' => $inventory->quantity]);
            }
            
            // Handle new images
            if ($request->hasFile('images')) {
                $imageCount = count($request->file('images'));
                Log::info('Processing new images for update', ['count' => $imageCount, 'item_id' => $item->item_id]);
                
                foreach ($request->file('images') as $index => $file) {
                    try {
                        // Verify the file is valid
                        if (!$file->isValid()) {
                            Log::error('Invalid file upload during update', [
                                'item_id' => $item->item_id,
                                'index' => $index,
                                'error' => $file->getErrorMessage()
                            ]);
                            continue;
                        }
                        
                        Log::info('Processing image for update', [
                            'item_id' => $item->item_id,
                            'index' => $index, 
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'error' => $file->getError()
                        ]);
                        
                        // Store the file
                        $path = $file->store('items', 'public');
                        Log::info('File stored at path during update', ['path' => $path]);
                        
                        if (!$path) {
                            Log::error('Failed to store file during update', [
                                'item_id' => $item->item_id,
                                'index' => $index,
                                'original_name' => $file->getClientOriginalName()
                            ]);
                            continue;
                        }
                        
                        $image = ItemImage::create([
                            'item_id' => $item->item_id,
                            'image_path' => $path
                        ]);
                        
                        Log::info('New image stored during update', [
                            'item_id' => $item->item_id,
                            'image_id' => $image->image_id,
                            'path' => $path
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to process image during update', [
                            'item_id' => $item->item_id,
                            'index' => $index,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } else {
                Log::info('No new images provided for update', ['item_id' => $item->item_id]);
            }
            
            DB::commit();
            Log::info('Product updated successfully', ['item_id' => $item->item_id]);
            
            return redirect()->route('admin.items.index')
                ->with('success', 'Product updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating item', [
                'item_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['images'])
            ]);
            
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

            // Don't delete images from storage during soft delete
            // Just soft delete the item and its related models

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
                ->with(['groups', 'inventory'])
                ->get();
            
            // Load images with withTrashed to include soft-deleted images
            foreach ($trashedItems as $item) {
                $item->load(['images' => function($query) {
                    $query->withTrashed();
                }]);
            }
            
            return DataTables::of($trashedItems)
                ->addColumn('image', function ($item) {
                    $image = $item->images->first();
                    if ($image && Storage::disk('public')->exists($image->image_path)) {
                        return '<img src="' . asset('storage/' . $image->image_path) . '" alt="' . $item->item_name . '" class="h-12 w-12 object-cover rounded-lg shadow-sm">';
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
                    $restoreBtn = '<button data-id="' . $item->item_id . '" class="restore-btn mr-1">
                                <i class="fas fa-trash-restore mr-1"></i> Restore
                            </button>';
                    $deleteBtn = '<button data-id="' . $item->item_id . '" class="force-delete-btn">
                                <i class="fas fa-trash mr-1"></i> Delete
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
            DB::beginTransaction();
            
            $item = Item::onlyTrashed()->findOrFail($id);
            
            // Restore the item
            $item->restore();
            
            // Restore associated images
            ItemImage::withTrashed()
                ->where('item_id', $id)
                ->restore();
            
            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product restored successfully.'
                ]);
            }
            
            return redirect()->route('admin.items.trash')
                ->with('success', 'Product restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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

    /**
     * Delete an individual image from a product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage($id)
    {
        try {
            // Log the incoming image ID for debugging
            Log::info('Attempting to delete image with ID: ' . $id);
            
            $image = ItemImage::withTrashed()->findOrFail($id);
            $itemId = $image->item_id;
            
            // Log the image data for debugging
            Log::info('Found image:', [
                'image_id' => $image->image_id,
                'item_id' => $image->item_id,
                'path' => $image->image_path
            ]);
            
            // Check if file exists and delete it
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
                Log::info('Deleted physical image file');
            } else {
                Log::info('Physical image file not found');
            }
            
            // Force delete the image record
            $image->forceDelete();
            Log::info('Deleted image record from database');
            
            return redirect()->back()
                ->with('success', 'Image removed successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete image: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }
} 