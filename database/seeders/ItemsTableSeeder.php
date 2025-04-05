<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Processors
            [
                'item_name' => 'Intel Core i9-13900K',
                'item_description' => '24-core, 32-thread processor with 5.8GHz max turbo frequency',
                'price' => 589.99,
            ],
            [
                'item_name' => 'AMD Ryzen 9 7950X',
                'item_description' => '16-core, 32-thread processor with 5.7GHz max boost clock',
                'price' => 699.99,
            ],
            // Graphics Cards
            [
                'item_name' => 'NVIDIA RTX 4090',
                'item_description' => '24GB GDDR6X, 384-bit memory interface',
                'price' => 1599.99,
            ],
            [
                'item_name' => 'AMD Radeon RX 7900 XTX',
                'item_description' => '24GB GDDR6, 384-bit memory interface',
                'price' => 999.99,
            ],
            // Motherboards
            [
                'item_name' => 'ASUS ROG Maximus Z790 Hero',
                'item_description' => 'Intel Z790 chipset, LGA 1700 socket',
                'price' => 499.99,
            ],
            [
                'item_name' => 'MSI MPG X670E Carbon WiFi',
                'item_description' => 'AMD X670 chipset, AM5 socket',
                'price' => 449.99,
            ],
            // Memory
            [
                'item_name' => 'Corsair Dominator Platinum RGB 32GB',
                'item_description' => 'DDR5-6000, CL36, 2x16GB kit',
                'price' => 249.99,
            ],
            [
                'item_name' => 'G.Skill Trident Z5 RGB 64GB',
                'item_description' => 'DDR5-6400, CL32, 2x32GB kit',
                'price' => 399.99,
            ],
            // Storage
            [
                'item_name' => 'Samsung 990 Pro 2TB',
                'item_description' => 'PCIe 4.0 NVMe SSD, 7450MB/s read, 6900MB/s write',
                'price' => 249.99,
            ],
            [
                'item_name' => 'WD Black SN850X 4TB',
                'item_description' => 'PCIe 4.0 NVMe SSD, 7300MB/s read, 6600MB/s write',
                'price' => 399.99,
            ],
            // Power Supplies
            [
                'item_name' => 'Corsair RM1000x',
                'item_description' => '1000W 80+ Gold Fully Modular PSU',
                'price' => 199.99,
            ],
            [
                'item_name' => 'Seasonic PRIME TX-1300',
                'item_description' => '1300W 80+ Titanium Fully Modular PSU',
                'price' => 299.99,
            ],
            // Cooling
            [
                'item_name' => 'NZXT Kraken Z73',
                'item_description' => '360mm AIO Liquid Cooler with LCD Display',
                'price' => 279.99,
            ],
            [
                'item_name' => 'Noctua NH-D15',
                'item_description' => 'Premium Dual-Tower CPU Cooler',
                'price' => 109.99,
            ],
            // Peripherals
            [
                'item_name' => 'Logitech G Pro X Superlight',
                'item_description' => 'Wireless Gaming Mouse, 25K DPI',
                'price' => 149.99,
            ],
            [
                'item_name' => 'Corsair K100 RGB',
                'item_description' => 'Mechanical Gaming Keyboard, OPX Optical Switches',
                'price' => 229.99,
            ],
        ];

        foreach ($items as $item) {
            DB::table('items')->insert([
                'item_name' => $item['item_name'],
                'item_description' => $item['item_description'],
                'price' => $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 