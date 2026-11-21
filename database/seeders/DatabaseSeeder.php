<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PublicationSeeder::class,
            SavedPublicationSeeder::class,
            TagPublicationSeeder::class,
            ReactionSeeder::class,
            CommentSeeder::class,
            // After the publications: a report points at one of them
            ComplaintSeeder::class,
        ]);
    }
}
