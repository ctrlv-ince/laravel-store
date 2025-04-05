<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $isAdmin = $user->email === 'admin@techstore.com';
            
            DB::table('accounts')->insert([
                'user_id' => $user->user_id,
                'username' => strtolower(str_replace(' ', '', $user->first_name . $user->last_name)),
                'password' => $isAdmin ? Hash::make('admin123') : Hash::make('user123'),
                'role' => $isAdmin ? 'admin' : 'user',
                'profile_img' => 'default.jpg',
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 