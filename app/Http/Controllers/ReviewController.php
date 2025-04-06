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
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check if user has already reviewed this item
        $existingReview = Review::where('account_id', Auth::user()->account->account_id)
            ->where('item_id', $item->item_id)
            ->first();

        if ($existingReview) {
            // Update the existing review
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'create_at' => now(),
                'update_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your review has been updated!'
            ]);
        } else {
            // Create a new review
            Review::create([
                'account_id' => Auth::user()->account->account_id,
                'item_id' => $item->item_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'create_at' => now(),
                'update_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your review has been submitted!'
            ]);
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
