<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get();
        $items = DB::table('items')->get();
        $accounts = DB::table('accounts')->get();

        $cartItems = [
            [
                'account_id' => $accounts->first()->account_id,
                'item_id' => $items->where('item_name', 'Intel Core i9-13900K')->first()->item_id,
                'quantity' => 1,
                'date_placed' => now(),
            ],
            [
                'account_id' => $accounts->first()->account_id,
                'item_id' => $items->where('item_name', 'NVIDIA RTX 4090')->first()->item_id,
                'quantity' => 1,
                'date_placed' => now(),
            ],
            [
                'account_id' => $accounts->first()->account_id,
                'item_id' => $items->where('item_name', 'Samsung 990 Pro 2TB')->first()->item_id,
                'quantity' => 2,
                'date_placed' => now(),
            ],
        ];

        foreach ($cartItems as $cartItem) {
            DB::table('carts')->insert([
                'account_id' => $cartItem['account_id'],
                'item_id' => $cartItem['item_id'],
                'quantity' => $cartItem['quantity'],
                'date_placed' => $cartItem['date_placed'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 