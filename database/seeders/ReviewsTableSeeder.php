<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get();
        $items = DB::table('items')->get();
        $accounts = DB::table('accounts')->get();

        $reviews = [
            [
                'item_id' => $items->where('item_name', 'Intel Core i9-13900K')->first()->item_id,
                'account_id' => $accounts->first()->account_id,
                'comment' => 'Amazing processor! The performance is outstanding for both gaming and productivity tasks.',
                'rating' => 5,
                'create_at' => now(),
                'update_at' => now(),
            ],
            [
                'item_id' => $items->where('item_name', 'AMD Ryzen 9 7950X')->first()->item_id,
                'account_id' => $accounts->first()->account_id,
                'comment' => 'Great value for money. The multi-core performance is exceptional.',
                'rating' => 4,
                'create_at' => now(),
                'update_at' => now(),
            ],
            [
                'item_id' => $items->where('item_name', 'NVIDIA RTX 4090')->first()->item_id,
                'account_id' => $accounts->first()->account_id,
                'comment' => 'The most powerful GPU I\'ve ever used. Perfect for 4K gaming.',
                'rating' => 5,
                'create_at' => now(),
                'update_at' => now(),
            ],
            [
                'item_id' => $items->where('item_name', 'Samsung 990 Pro 2TB')->first()->item_id,
                'account_id' => $accounts->first()->account_id,
                'comment' => 'Incredible read/write speeds. Perfect for gaming and content creation.',
                'rating' => 5,
                'create_at' => now(),
                'update_at' => now(),
            ],
        ];

        foreach ($reviews as $review) {
            DB::table('reviews')->insert($review);
        }
    }
} 