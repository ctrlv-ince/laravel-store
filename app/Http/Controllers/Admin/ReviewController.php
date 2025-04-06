<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['item', 'account.user'])->select('reviews.*');
            
            return DataTables::of($reviews)
                ->addColumn('product', function ($review) {
                    return $review->item ? $review->item->item_name : 'Unknown Product';
                })
                ->addColumn('customer', function ($review) {
                    if ($review->account && $review->account->user) {
                        return $review->account->user->first_name . ' ' . $review->account->user->last_name;
                    }
                    return 'Unknown Customer';
                })
                ->addColumn('rating_stars', function ($review) {
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $review->rating) {
                            $stars .= '<i class="fas fa-star text-yellow-400"></i>';
                        } else {
                            $stars .= '<i class="far fa-star text-gray-400"></i>';
                        }
                    }
                    return $stars;
                })
                ->addColumn('date', function ($review) {
                    if ($review->created_at) {
                        return $review->created_at->format('M d, Y H:i');
                    }
                    return 'N/A';
                })
                ->addColumn('actions', function ($review) {
                    $deleteBtn = '<button type="button" class="delete-review" data-id="' . $review->review_id . '"><i class="fas fa-trash-alt mr-1"></i> Delete</button>';
                    return $deleteBtn;
                })
                ->rawColumns(['rating_stars', 'actions'])
                ->make(true);
        }
        
        return view('admin.reviews.index');
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $itemId = $review->item_id; // Store the item ID for redirect
            $review->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review deleted successfully!'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Review deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting review: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the review: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the review: ' . $e->getMessage());
        }
    }
} 