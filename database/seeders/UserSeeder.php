<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'somnang',
                'email' => 'somnang@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('somnang123'),
                'role' => 'user', // កំណត់ជា User ធម្មតា
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin', // <--- ជួរនេះហើយដែលធ្វើឱ្យអ្នកមានសិទ្ធិចូល Admin បាន
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}