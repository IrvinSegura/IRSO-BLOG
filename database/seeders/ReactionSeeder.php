<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        date_default_timezone_set('America/Mexico_City');
        DB::table('reaction')->insert([
            'publication_id' => 1,
            'user_id' => 1,
            'type_reaction' => 'like',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
