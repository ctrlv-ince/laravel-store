<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing reviews
        DB::table('reviews')->truncate();
        
        $users = DB::table('users')->get();
        $items = DB::table('items')->get();
        $accounts = DB::table('accounts')->get();

        // Create review templates with varied content
        $positiveComments = [
            'Absolutely love this product! Exceeded my expectations in every way.',
            'Great value for money. Would definitely recommend to others.',
            'Top-notch quality and performance. A must-have for tech enthusiasts.',
            'Shipping was fast and the product works perfectly. Very satisfied!',
            'This is exactly what I was looking for. Perfect for my setup.',
            'Amazing build quality and works flawlessly. Very impressed!',
            'The performance is outstanding, definitely worth the investment.',
            'Easy to set up and works as advertised. Very happy with my purchase.',
            'Exceptional product that delivers on all its promises.',
            'Best purchase I\'ve made this year. Absolutely no regrets!'
        ];

        $averageComments = [
            'Decent product for the price. Works as expected.',
            'Good but not great. A few minor issues but overall satisfied.',
            'Does the job but nothing extraordinary. Fair value.',
            'Functional but could use some improvements in design.',
            'Adequate performance for casual use but might not satisfy professionals.',
            'Middle-of-the-road product. Neither impressive nor disappointing.',
            'Reasonable quality for the price point. Gets the job done.',
            'Some pros and cons, but overall a satisfactory purchase.',
            'Meets basic requirements but doesn\'t exceed expectations.',
            'Acceptable performance with some limitations.'
        ];

        $negativeComments = [
            'Disappointed with the quality. Not worth the price.',
            'Had issues right from the start. Would not recommend.',
            'Expected better performance for the cost. Rather underwhelming.',
            'The product description was misleading. Not as advertised.',
            'Poor build quality and customer support was unhelpful.',
            'Several defects noticed after a week of use. Very frustrated.',
            'Overpriced for what it offers. Look elsewhere.',
            'Doesn\'t perform as promised. Save your money.',
            'Too many compatibility issues. Not user-friendly at all.',
            'Regret this purchase. Will be looking for alternatives.'
        ];

        $reviews = [];

        // Generate reviews for each item
        foreach ($items as $item) {
            // Determine how many reviews this item will have (1-5)
            $reviewCount = rand(1, 5);

            // For each review for this item
            for ($i = 0; $i < $reviewCount; $i++) {
                // Pick a random account
                $account = Arr::random($accounts->toArray());
                
                // Generate a random rating (1-5)
                $rating = rand(1, 5);
                
                // Select an appropriate comment based on the rating
                if ($rating >= 4) {
                    $comment = Arr::random($positiveComments);
                } elseif ($rating >= 2) {
                    $comment = Arr::random($averageComments);
                } else {
                    $comment = Arr::random($negativeComments);
                }
                
                // Create a random date within the last 6 months
                $date = Carbon::now()->subDays(rand(1, 180));
                
                $reviews[] = [
                    'item_id' => $item->item_id,
                    'account_id' => $account->account_id,
                    'comment' => $comment,
                    'rating' => $rating,
                    'created_at' => $date,
                    'updated_at' => $date,
                    'create_at' => $date,
                    'update_at' => $date,
                ];
            }
        }

        // Insert all reviews
        foreach ($reviews as $review) {
            DB::table('reviews')->insert($review);
        }
        
        echo "Created " . count($reviews) . " reviews for " . $items->count() . " items.\n";
    }
} 