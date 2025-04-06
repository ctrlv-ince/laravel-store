<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users with DataTables integration.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('account')->select('users.*');
            
            return DataTables::of($users)
                ->addColumn('role', function ($user) {
                    return $user->account ? $user->account->role : 'N/A';
                })
                ->addColumn('status', function ($user) {
                    return $user->account ? $user->account->account_status : 'N/A';
                })
                ->addColumn('actions', function ($user) {
                    $editBtn = '<a href="' . route('admin.users.edit', $user->user_id) . '" class="btn btn-sm btn-primary mr-1">Manage Account</a>';
                    
                    return $editBtn;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('admin.users.index');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::with('account')->findOrFail($id);
        
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,inactive'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            $user = User::findOrFail($id);
            
            // Only update account-related information, not personal details
            if ($user->account) {
                $user->account->update([
                    'role' => $request->input('role'),
                    'account_status' => $request->input('status')
                ]);
            } else {
                Account::create([
                    'user_id' => $user->user_id,
                    'username' => $user->email, // Fallback username if none exists
                    'password' => $user->password,
                    'role' => $request->input('role'),
                    'account_status' => $request->input('status')
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User account updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the status of a user via AJAX.
     * 
     * Setting a user to inactive will prevent them from logging into the application.
     * Their session will be terminated and they will be redirected to the login page
     * with an error message if they are currently logged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'status' => 'required|in:active,inactive'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        
        try {
            $user = User::findOrFail($request->user_id);
            
            if ($user->account) {
                $user->account->update(['account_status' => $request->status]);
                return response()->json(['success' => true, 'message' => 'User status updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'User has no account record.'], 404);
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while updating user status.'], 500);
        }
    }

    /**
     * Update the role of a user via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,user_id',
            'role' => 'required|in:user,admin'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        
        try {
            $user = User::findOrFail($request->user_id);
            
            if ($user->account) {
                $user->account->update(['role' => $request->role]);
                return response()->json(['success' => true, 'message' => 'User role updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'User has no account record.'], 404);
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating user role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while updating user role.'], 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('account')->findOrFail($id);
        $account = $user->account;
        
        return view('admin.users.show', compact('user', 'account'));
    }
} 