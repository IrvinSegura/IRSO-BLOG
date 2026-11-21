<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\SavedPublication;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavedPublicationSeeder extends Seeder
{
    /**
     * The demo user's reading list. Safe to run again.
     */
    public function run(): void
    {
        $reader = User::where('email', 'usuario@irsoblog.test')->first();
        if (! $reader) {
            return;
        }

        Publication::whereIn('slug', ['cinco-mercados-de-mexico-que-tienes-que-visitar', 'habitos-sencillos-para-dormir-mejor'])
            ->pluck('id')
            ->each(fn ($id) => SavedPublication::firstOrCreate(['user_id' => $reader->id, 'publication_id' => $id]));
    }
}
