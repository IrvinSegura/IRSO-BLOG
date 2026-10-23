<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        date_default_timezone_set('America/Mexico_City');
        DB::table('comments')->insert([
            'publication_id' => 1,
            'user_id' => 2,
            'comment' => 'Buena noticia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
