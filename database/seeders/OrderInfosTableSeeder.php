<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = DB::table('orders')->get();
        $items = DB::table('items')->get();

        $orderInfos = [
            // First order (completed)
            [
                'order_id' => $orders->first()->order_id,
                'item_id' => $items->where('item_name', 'Intel Core i9-13900K')->first()->item_id,
                'quantity' => 1,
                'created' => now()->subDays(7),
            ],
            [
                'order_id' => $orders->first()->order_id,
                'item_id' => $items->where('item_name', 'NVIDIA RTX 4090')->first()->item_id,
                'quantity' => 1,
                'created' => now()->subDays(7),
            ],
            // Second order (processing)
            [
                'order_id' => $orders->last()->order_id,
                'item_id' => $items->where('item_name', 'AMD Ryzen 9 7950X')->first()->item_id,
                'quantity' => 1,
                'created' => now()->subDays(3),
            ],
        ];

        foreach ($orderInfos as $orderInfo) {
            DB::table('orderinfos')->insert($orderInfo);
        }
    }
} 