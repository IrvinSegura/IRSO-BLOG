<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * A sample "like" from the administrator. Safe to run again.
     */
    public function run(): void
    {
        $publication = Publication::where('slug', 'el-nuevo-iphone-12')->first();
        $admin = User::where('email', 'admin@irsoblog.test')->first();

        if ($publication && $admin) {
            Reaction::firstOrCreate(['publication_id' => $publication->id, 'user_id' => $admin->id, 'type_reaction' => 'like']);
        }
    }
}
