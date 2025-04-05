<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = DB::table('items')->get();

        foreach ($items as $item) {
            // Generate random quantity between 10 and 50
            $quantity = rand(10, 50);

            DB::table('inventories')->insert([
                'item_id' => $item->item_id,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 