<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DB::table('item_groups')->truncate();
        DB::table('groups')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $groups = [
            [
                'group_name' => 'Arduino',
                'group_description' => 'Official Arduino boards and compatible variants',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_name' => 'Raspberry Pi',
                'group_description' => 'Raspberry Pi single-board computers and accessories',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_name' => 'ESP32',
                'group_description' => 'ESP32 development boards and modules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_name' => 'ESP8266',
                'group_description' => 'ESP8266 WiFi modules and development boards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_name' => 'STM32',
                'group_description' => 'STM32 microcontroller development boards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'group_name' => 'BeagleBone',
                'group_description' => 'BeagleBone single-board computers and accessories',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('groups')->insert($groups);
    }
} 