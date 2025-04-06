<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Log the request for debugging
                Log::info('Groups index AJAX request', [
                    'request_data' => $request->all()
                ]);
                
                $groups = Group::withCount('items');
                
                // Log the query for debugging
                Log::info('Groups query', [
                    'sql' => $groups->toSql(),
                    'bindings' => $groups->getBindings()
                ]);
                
                $result = DataTables::of($groups)
                    ->addColumn('action', function ($group) {
                        $editBtn = '<button class="bg-blue-500 hover:bg-blue-600 text-white text-xs p-1 rounded mr-1 edit-btn" 
                                    data-id="'.$group->group_id.'" 
                                    data-name="'.$group->group_name.'" 
                                    data-description="'.$group->group_description.'">
                                    <i class="fas fa-edit"></i> Edit
                                </button>';
                        $deleteBtn = '<button class="bg-red-500 hover:bg-red-600 text-white text-xs p-1 rounded delete-btn" 
                                    data-id="'.$group->group_id.'">
                                    <i class="fas fa-trash"></i> Delete
                                </button>';
                        return $editBtn . $deleteBtn;
                    })
                    ->editColumn('created_at', function ($group) {
                        return $group->created_at ? $group->created_at->format('M d, Y') : 'N/A';
                    })
                    ->rawColumns(['action']);
                
                // Output the result payload for debugging
                $response = $result->make(true);
                Log::info('DataTables response', [
                    'data_count' => count($response->getData()->data),
                    'recordsTotal' => $response->getData()->recordsTotal,
                    'sample_row' => !empty($response->getData()->data) ? json_encode($response->getData()->data[0]) : 'No data'
                ]);
                
                return $response;
            } catch (\Exception $e) {
                Log::error('Error in Groups index:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('admin.groups.index');
    }

    /**
     * Show the form for creating a new group.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255|unique:groups,group_name',
            'group_description' => 'nullable|string',
        ]);

        try {
            Group::create([
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
            ]);

            return redirect()->route('admin.groups.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage());
            return redirect()->route('admin.groups.index')
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified group.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $group = Group::findOrFail($id);
        
        return view('admin.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $request->validate([
            'group_name' => 'required|string|max:255|unique:groups,group_name,' . $id . ',group_id',
            'group_description' => 'nullable|string',
        ]);

        try {
            $group->update([
                'group_name' => $request->group_name,
                'group_description' => $request->group_description,
            ]);

            return redirect()->route('admin.groups.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update category: ' . $e->getMessage());
            return redirect()->route('admin.groups.index')
                ->with('error', 'Failed to update category. Please try again.');
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
            // Start a database transaction
            DB::beginTransaction();
            
            // Find the category
            $group = Group::findOrFail($id);
            
            // Set group_id to NULL for items in this category
            $group->items()->update(['group_id' => null]);
            
            // Delete the category
            $group->delete();
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('admin.groups.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            
            Log::error('Failed to delete category: ' . $e->getMessage());
            return redirect()->route('admin.groups.index')
                ->with('error', 'Failed to delete category. Please try again.');
        }
    }
} 