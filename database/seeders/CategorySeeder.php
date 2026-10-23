<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Default categories of the blog.
     *
     * @var array<int, string>
     */
    private const NAMES = [
        'Deportes', 'Entretenimiento', 'Política', 'Economía', 'Salud', 'Ciencia', 'Educación', 'Cultura',
        'Medio Ambiente', 'Mundo', 'Sociedad', 'Viajes', 'Estilo de vida', 'Música', 'Arte', 'Gastronomía',
    ];

    /**
     * Run the database seeds. Safe to run again: existing categories are not duplicated.
     */
    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Category::withTrashed()->firstOrCreate(['name' => $name]);
        }
    }
}
