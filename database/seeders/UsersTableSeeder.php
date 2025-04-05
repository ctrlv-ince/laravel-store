<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@techstore.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'age' => 30,
                'sex' => 'Male',
                'phone_number' => '1234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'user@techstore.com',
                'password' => Hash::make('user123'),
                'email_verified_at' => now(),
                'age' => 25,
                'sex' => 'Female',
                'phone_number' => '0987654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
} 