<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accounts = Account::with('user');
            
            return DataTables::of($accounts)
                ->addColumn('full_name', function($account) {
                    return $account->user->first_name . ' ' . $account->user->last_name;
                })
                ->addColumn('profile_image', function($account) {
                    return asset('storage/' . $account->profile_img);
                })
                ->addColumn('action', function($account) {
                    return view('accounts.actions', compact('account'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'required|integer|min:1|max:150',
            'sex' => 'required|in:Male,Female',
            'phone_number' => 'required|string|max:12|unique:users',
            'username' => 'required|string|max:50|unique:accounts',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'phone_number' => $request->phone_number
        ]);

        $profileImagePath = $request->file('profile_image')->store('profiles', 'public');

        $account = Account::create([
            'user_id' => $user->user_id,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'profile_img' => $profileImagePath,
            'account_status' => 'active'
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        $account->load('user');
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $account->load('user');
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'age' => 'required|integer|min:1|max:150',
            'sex' => 'required|in:Male,Female',
            'phone_number' => 'required|string|max:12|unique:users,phone_number,' . $account->user->user_id . ',user_id',
            'username' => 'required|string|max:50|unique:accounts,username,' . $account->account_id . ',account_id',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $account->user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'sex' => $request->sex,
            'phone_number' => $request->phone_number
        ]);

        $accountData = [
            'username' => $request->username
        ];

        if ($request->filled('password')) {
            $accountData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            Storage::disk('public')->delete($account->profile_img);
            $accountData['profile_img'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $account->update($accountData);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        Storage::disk('public')->delete($account->profile_img);
        $account->delete();
        $account->user->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }

    public function updateStatus(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'account_status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $account->update([
            'account_status' => $request->account_status
        ]);

        return response()->json(['message' => 'Account status updated successfully.']);
    }

    public function updateRole(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,user'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $account->update([
            'role' => $request->role
        ]);

        return response()->json(['message' => 'Account role updated successfully.']);
    }
}
