<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Manish Thapa',
                'role' => 'Super Admin',
                'email' => 'manish@gmail.com',
                'phone' => '9800000001',
                'password' => Hash::make('manish123'),
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mahesh Karki',
                'role' => 'Electronics',
                'email' => 'mahesh@gmail.com',
                'phone' => '9800000002',
                'password' => Hash::make('mahesh123'),
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karan Khatiwada',
                'role' => 'Electrical',
                'email' => 'karan@gmail.com',
                'phone' => '9800000003',
                'password' => Hash::make('karan123'),
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
