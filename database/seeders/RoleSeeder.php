<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        date_default_timezone_set('America/Mexico_City');
        DB::table('role')->insert([
            'name' => 'Administrador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role')->insert([
            'name' => 'Editor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('role')->insert([
            'name' => 'Usuario',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
