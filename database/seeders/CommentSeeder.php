<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Sample conversations between the demo accounts: [publication slug, author email, text].
     *
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    public const SAMPLES = [
        ['el-nuevo-iphone-12', 'editor@irsoblog.test', 'Buena noticia'],
        ['mis-primeros-pasos-en-irso-blog', 'admin@irsoblog.test', '¡Bienvenido! Nos encantó tu primera publicación.'],
        ['mis-primeros-pasos-en-irso-blog', 'editor@irsoblog.test', 'Muy buena portada, sigue escribiendo.'],
        ['cinco-mercados-de-mexico-que-tienes-que-visitar', 'usuario@irsoblog.test', 'El de Tlacolula es mi favorito.'],
        ['una-ruta-por-la-huasteca-potosina', 'editor@irsoblog.test', '¿Qué temporada recomiendas para ir?'],
    ];

    /**
     * Run the database seeds. Safe to run again.
     */
    public function run(): void
    {
        foreach (self::SAMPLES as [$slug, $email, $text]) {
            $publication = Publication::where('slug', $slug)->first();
            $user = User::where('email', $email)->first();

            if ($publication && $user) {
                Comment::firstOrCreate(['publication_id' => $publication->id, 'user_id' => $user->id, 'comment' => $text]);
            }
        }
    }
}
