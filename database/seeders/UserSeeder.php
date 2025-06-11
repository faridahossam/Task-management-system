<?php

namespace Database\Seeders;

use Faker\Factory;
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
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $data[] = ['name' => 'manager', 'email' => 'manager@gmail.com', 'password' => bcrypt('password'), 'created_at' => now(),'updated_at' => now(), ];
        DB::table('users')->insert($data);
    }
}
