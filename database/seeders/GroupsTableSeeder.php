<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'group_name' => 'Processors',
                'group_description' => 'Central Processing Units (CPUs) for computers',
            ],
            [
                'group_name' => 'Graphics Cards',
                'group_description' => 'Graphics Processing Units (GPUs) for gaming and professional use',
            ],
            [
                'group_name' => 'Motherboards',
                'group_description' => 'Main circuit boards for computer systems',
            ],
            [
                'group_name' => 'Memory',
                'group_description' => 'RAM modules for computer systems',
            ],
            [
                'group_name' => 'Storage',
                'group_description' => 'SSDs and HDDs for data storage',
            ],
            [
                'group_name' => 'Power Supplies',
                'group_description' => 'Power supply units for computer systems',
            ],
            [
                'group_name' => 'Cooling',
                'group_description' => 'Cooling solutions for computer components',
            ],
            [
                'group_name' => 'Peripherals',
                'group_description' => 'Computer peripherals and accessories',
            ],
        ];

        foreach ($groups as $group) {
            DB::table('groups')->insert([
                'group_name' => $group['group_name'],
                'group_description' => $group['group_description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 