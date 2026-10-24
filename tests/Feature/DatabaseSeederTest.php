<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CommentSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PublicationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_from_the_readme_exist_with_their_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (UserSeeder::ACCOUNTS as $email => $account) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertSame($account['rol'], (int) $user->rol);
            $this->assertTrue(Hash::check(UserSeeder::PASSWORD, $user->password));
        }

        $this->assertSame(Role::USER, (int) Publication::where('slug', 'mis-primeros-pasos-en-irso-blog')->first()->user->rol);
    }

    public function test_seeding_twice_does_not_duplicate_anything(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, Role::count());
        $this->assertSame(3, User::count());
        $this->assertSame(16, Category::count());
        $this->assertSame(count(PublicationSeeder::SAMPLES), Publication::count());
        $this->assertSame(count(CommentSeeder::SAMPLES), Comment::count());
    }
}
