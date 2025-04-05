<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = DB::table('accounts')->get();

        $orders = [
            [
                'account_id' => $accounts->first()->account_id,
                'date_ordered' => now()->subDays(7),
                'status' => 'completed',
                'total_amount' => 2189.97,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'account_id' => $accounts->first()->account_id,
                'date_ordered' => now()->subDays(3),
                'status' => 'pending',
                'total_amount' => 699.99,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ];

        foreach ($orders as $order) {
            DB::table('orders')->insert($order);
        }
    }
} 