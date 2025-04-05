<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DB::table('item_groups')->truncate();
        DB::table('item_images')->truncate();
        DB::table('inventories')->truncate();
        DB::table('orderinfos')->truncate();
        DB::table('reviews')->truncate();
        DB::table('carts')->truncate();
        DB::table('items')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = [
            // Arduino
            [
                'item_name' => 'Arduino Uno R3',
                'item_description' => 'ATmega328P microcontroller, 14 digital I/O pins, 6 analog inputs',
                'price' => 24.99,
                'quantity' => 50,
                'group_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Arduino Mega 2560',
                'item_description' => 'ATmega2560 microcontroller, 54 digital I/O pins, 16 analog inputs',
                'price' => 39.99,
                'quantity' => 30,
                'group_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Raspberry Pi
            [
                'item_name' => 'Raspberry Pi 4 Model B',
                'item_description' => '4GB RAM, 1.5GHz quad-core CPU, dual-band WiFi, Bluetooth 5.0',
                'price' => 55.00,
                'quantity' => 25,
                'group_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Raspberry Pi Pico',
                'item_description' => 'RP2040 microcontroller, dual-core Arm Cortex-M0+',
                'price' => 4.00,
                'quantity' => 100,
                'group_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ESP32
            [
                'item_name' => 'ESP32 DevKit V1',
                'item_description' => 'Dual-core Xtensa LX6, WiFi and Bluetooth, 38 GPIO pins',
                'price' => 12.99,
                'quantity' => 40,
                'group_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'ESP32-CAM',
                'item_description' => 'ESP32 with OV2640 camera, WiFi, Bluetooth, microSD slot',
                'price' => 9.99,
                'quantity' => 35,
                'group_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ESP8266
            [
                'item_name' => 'NodeMCU ESP8266',
                'item_description' => 'ESP-12E module, WiFi, 11 GPIO pins, microUSB',
                'price' => 8.99,
                'quantity' => 45,
                'group_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Wemos D1 Mini',
                'item_description' => 'ESP8266 based, compact size, WiFi, 11 GPIO pins',
                'price' => 6.99,
                'quantity' => 50,
                'group_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // STM32
            [
                'item_name' => 'STM32F103C8T6',
                'item_description' => 'Blue Pill development board, ARM Cortex-M3, 72MHz',
                'price' => 7.99,
                'quantity' => 30,
                'group_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'STM32F407VET6',
                'item_description' => 'Black Pill development board, ARM Cortex-M4, 168MHz',
                'price' => 14.99,
                'quantity' => 25,
                'group_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // BeagleBone
            [
                'item_name' => 'BeagleBone Black',
                'item_description' => '1GHz ARM Cortex-A8, 512MB RAM, 4GB eMMC',
                'price' => 55.00,
                'quantity' => 20,
                'group_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'BeagleBone AI',
                'item_description' => 'Dual Cortex-A15, 1GB RAM, 16GB eMMC, AI capabilities',
                'price' => 129.00,
                'quantity' => 15,
                'group_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert items
        foreach ($items as $item) {
            $itemId = DB::table('items')->insertGetId([
                'item_name' => $item['item_name'],
                'item_description' => $item['item_description'],
                'price' => $item['price'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ]);

            // Create inventory record
            DB::table('inventories')->insert([
                'item_id' => $itemId,
                'quantity' => $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link to group
            DB::table('item_groups')->insert([
                'item_id' => $itemId,
                'group_id' => $item['group_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 