<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * Sample reactions from the demo accounts: [publication slug, reader email, reaction].
     */
    public const SAMPLES = [
        ['el-nuevo-iphone-12', 'admin@irsoblog.test', 'love'],
        ['el-nuevo-iphone-12', 'usuario@irsoblog.test', 'fire'],
        ['mis-primeros-pasos-en-irso-blog', 'editor@irsoblog.test', 'clap'],
        ['una-ruta-por-la-huasteca-potosina', 'admin@irsoblog.test', 'love'],
    ];

    /**
     * Safe to run again: each reader keeps a single reaction per publication.
     */
    public function run(): void
    {
        foreach (self::SAMPLES as [$slug, $email, $type]) {
            $publication = Publication::where('slug', $slug)->first();
            $user = User::where('email', $email)->first();

            if ($publication && $user) {
                Reaction::updateOrCreate(
                    ['publication_id' => $publication->id, 'user_id' => $user->id],
                    ['type_reaction' => $type],
                );
            }
        }
    }
}
