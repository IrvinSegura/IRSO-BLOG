<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        date_default_timezone_set('America/Mexico_City');
        $faker = Faker::create();
        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->email,
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'rol' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->email,
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'rol' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->email,
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'rol' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
