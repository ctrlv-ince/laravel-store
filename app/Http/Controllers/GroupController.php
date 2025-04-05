<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $groups = Group::withCount('items');
            
            return DataTables::of($groups)
                ->addColumn('action', function($group) {
                    return view('groups.actions', compact('group'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('groups.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|unique:groups|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Group::create([
            'group_name' => $request->group_name
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $group->load(['items.images', 'items.inventory']);
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|max:100|unique:groups,group_name,' . $group->group_id . ',group_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->update([
            'group_name' => $request->group_name
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Add items to group
     */
    public function addItems(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'exists:items,item_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->items()->attach($request->items);

        return redirect()->back()
            ->with('success', 'Items added to group successfully.');
    }

    /**
     * Remove items from group
     */
    public function removeItems(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'exists:items,item_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $group->items()->detach($request->items);

        return redirect()->back()
            ->with('success', 'Items removed from group successfully.');
    }
}
