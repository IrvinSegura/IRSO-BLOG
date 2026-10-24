<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    /**
     * Sample publications by the three demo accounts, so every role has content to manage.
     *
     * @var array<int, array{author: string, category: string, title: string, image: string, content: string, days_ago: int}>
     */
    public const SAMPLES = [
        [
            'author' => 'admin@irsoblog.test',
            'category' => 'Ciencia',
            'title' => 'El nuevo iPhone 12',
            'image' => 'https://cdn.pixabay.com/photo/2017/09/25/13/12/apple-2785074_960_720.jpg',
            'content' => 'El nuevo iPhone 12 es el mejor teléfono que ha lanzado Apple hasta la fecha.',
            'days_ago' => 9,
        ],
        [
            'author' => 'editor@irsoblog.test',
            'category' => 'Gastronomía',
            'title' => 'Cinco mercados de México que tienes que visitar',
            'image' => 'https://picsum.photos/seed/irso-mercados/1200/750',
            'content' => 'Del Mercado de San Juan a Tlacolula: colores, aromas y los mejores antojitos del país en un solo recorrido.',
            'days_ago' => 6,
        ],
        [
            'author' => 'admin@irsoblog.test',
            'category' => 'Salud',
            'title' => 'Hábitos sencillos para dormir mejor',
            'image' => 'https://picsum.photos/seed/irso-dormir/1200/750',
            'content' => 'Horarios fijos, menos pantallas por la noche y una habitación fresca: pequeños cambios que mejoran tu descanso.',
            'days_ago' => 4,
        ],
        [
            'author' => 'usuario@irsoblog.test',
            'category' => 'Viajes',
            'title' => 'Mis primeros pasos en IRSO BLOG',
            'image' => 'https://picsum.photos/seed/irso-blog/1200/750',
            'content' => 'Así fue escribir mi primera publicación: elegir una categoría, una portada y contar una buena historia.',
            'days_ago' => 3,
        ],
        [
            'author' => 'editor@irsoblog.test',
            'category' => 'Cultura',
            'title' => 'Lo que nos dejó el festival de cine de este año',
            'image' => 'https://picsum.photos/seed/irso-cine/1200/750',
            'content' => 'Óperas primas, documentales que conmueven y una selección latinoamericana que se robó la conversación.',
            'days_ago' => 1,
        ],
        [
            'author' => 'usuario@irsoblog.test',
            'category' => 'Viajes',
            'title' => 'Una ruta por la Huasteca Potosina',
            'image' => 'https://picsum.photos/seed/irso-huasteca/1200/750',
            'content' => 'Cascadas turquesa, sótanos llenos de aves y la mejor enchilada huasteca: cómo recorrerla en cuatro días.',
            'days_ago' => 0,
        ],
    ];

    /**
     * Run the database seeds. Safe to run again: publications are matched by slug.
     */
    public function run(): void
    {
        foreach (self::SAMPLES as $sample) {
            $author = User::where('email', $sample['author'])->first();
            $category = Category::where('name', $sample['category'])->first();

            if (! $author || ! $category) {
                continue;
            }

            $publication = Publication::firstOrCreate(['slug' => str($sample['title'])->slug()->value()], [
                'title' => $sample['title'],
                'src_img' => $sample['image'],
                'user_id' => $author->id,
                'category_id' => $category->id,
                'status' => Publication::STATUS_PUBLISHED,
                'content' => $sample['content'],
            ]);

            // Spread the dates so "Lo último" and the monthly chart look like a living blog.
            if ($publication->wasRecentlyCreated) {
                $date = now()->subDays($sample['days_ago'])->subHours(2);
                $publication->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
            }
        }
    }
}
