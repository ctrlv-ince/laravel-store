<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['account.user', 'item']);
            
            return DataTables::of($reviews)
                ->addColumn('reviewer', function($review) {
                    return $review->account->user->first_name . ' ' . $review->account->user->last_name;
                })
                ->addColumn('item_name', function($review) {
                    return $review->item->item_name;
                })
                ->addColumn('action', function($review) {
                    return view('reviews.actions', compact('review'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('reviews.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $review = Review::create([
            'account_id' => Auth::user()->account->account_id,
            'item_id' => $item->item_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'create_at' => now(),
            'update_at' => now()
        ]);

        return response()->json([
            'message' => 'Review posted successfully.',
            'review' => $review->load('account.user')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if ($review->account_id !== Auth::user()->account->account_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->update([
            'comment' => $request->comment,
            'rating' => $request->rating,
            'update_at' => now()
        ]);

        return response()->json([
            'message' => 'Review updated successfully.',
            'review' => $review->load('account.user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Only admin or the review owner can delete
        if (Auth::user()->account->role !== 'admin' && 
            $review->account_id !== Auth::user()->account->account_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    public function itemReviews(Item $item)
    {
        $reviews = $item->reviews()
            ->with('account.user')
            ->orderBy('create_at', 'desc')
            ->get();

        return response()->json(['reviews' => $reviews]);
    }
}
