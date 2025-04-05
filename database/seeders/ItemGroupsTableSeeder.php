<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all groups and items
        $groups = DB::table('groups')->get();
        $items = DB::table('items')->get();

        // Map items to their respective groups
        $itemGroups = [
            // Processors
            'Intel Core i9-13900K' => 'Processors',
            'AMD Ryzen 9 7950X' => 'Processors',
            
            // Graphics Cards
            'NVIDIA RTX 4090' => 'Graphics Cards',
            'AMD Radeon RX 7900 XTX' => 'Graphics Cards',
            
            // Motherboards
            'ASUS ROG Maximus Z790 Hero' => 'Motherboards',
            'MSI MPG X670E Carbon WiFi' => 'Motherboards',
            
            // Memory
            'Corsair Dominator Platinum RGB 32GB' => 'Memory',
            'G.Skill Trident Z5 RGB 64GB' => 'Memory',
            
            // Storage
            'Samsung 990 Pro 2TB' => 'Storage',
            'WD Black SN850X 4TB' => 'Storage',
            
            // Power Supplies
            'Corsair RM1000x' => 'Power Supplies',
            'Seasonic PRIME TX-1300' => 'Power Supplies',
            
            // Cooling
            'NZXT Kraken Z73' => 'Cooling',
            'Noctua NH-D15' => 'Cooling',
            
            // Peripherals
            'Logitech G Pro X Superlight' => 'Peripherals',
            'Corsair K100 RGB' => 'Peripherals',
        ];

        foreach ($items as $item) {
            $groupName = $itemGroups[$item->item_name];
            $group = $groups->firstWhere('group_name', $groupName);

            if ($group) {
                DB::table('item_groups')->insert([
                    'group_id' => $group->group_id,
                    'item_id' => $item->item_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
} 