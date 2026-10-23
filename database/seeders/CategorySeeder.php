<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('category')->insert([
            'name' => 'Deportes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Entretenimiento',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Política',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Economía',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Salud',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Ciencia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Educación',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Cultura',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Medio Ambiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Mundo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Sociedad',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Viajes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Estilo de vida',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Música',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Arte',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('category')->insert([
            'name' => 'Gastronomía',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
