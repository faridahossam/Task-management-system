<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'name' => 'User ' . ($i + 1),
                'email' => 'user' . ($i + 1) . '@gmail.com',
                'password' => bcrypt('password'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($data);
    }
}
