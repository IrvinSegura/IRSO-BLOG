<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private const ADMIN_ONLY = ['/admin/users', '/admin/users/hidden', '/admin/roles', '/admin/recycle-bin'];

    private const CONTENT_MANAGERS = ['/admin/publications', '/admin/categories', '/admin/statistics', '/admin/publications/export'];

    private const EVERYONE = ['/dashboard', '/blog', '/admin/publications/mine', '/admin/publications/create', '/user/profile'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['Administrador', 'Editor', 'Usuario'] as $name) {
            Role::create(['name' => $name]);
        }
    }

    public function test_regular_users_can_only_write_and_read_publications(): void
    {
        $this->actingAs(User::factory()->create(['rol' => Role::USER]));

        foreach (self::EVERYONE as $uri) {
            $this->get($uri)->assertOk();
        }

        foreach (array_diff([...self::ADMIN_ONLY, ...self::CONTENT_MANAGERS], ['/admin/publications']) as $uri) {
            $this->get($uri)->assertForbidden();
        }

        // The list of every publication sends them to their own instead of a 403.
        $this->get('/admin/publications')->assertRedirect('/admin/publications/mine');
    }

    public function test_regular_users_only_see_their_own_publications_in_the_panel(): void
    {
        $category = Category::create(['name' => 'Deportes']);
        $user = User::factory()->create(['rol' => Role::USER]);

        foreach ([$user, User::factory()->create()] as $author) {
            $author->publications()->create([
                'title' => $author->is($user) ? 'Escrita por mí' : 'Escrita por otra persona',
                'slug' => 'publicacion-'.$author->id,
                'src_img' => 'https://example.com/image.jpg',
                'category_id' => $category->id,
                'status' => 'publicado',
                'content' => 'Contenido',
            ]);
        }

        $this->actingAs($user);

        foreach (['/dashboard', '/admin/publications/mine'] as $uri) {
            $this->get($uri)->assertSee('Escrita por mí')->assertDontSee('Escrita por otra persona');
        }

        $this->get('/dashboard')->assertDontSee('href="'.route('admin.publications.index').'"', false);
    }

    public function test_regular_users_can_publish(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs(User::factory()->create(['rol' => Role::USER]))->post('/admin/publications', [
            'title' => 'Mi primera publicación',
            'category_id' => $category->id,
            'content' => 'Contenido',
            'src_img' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/admin/publications/mine');

        $this->assertDatabaseHas('publications', ['title' => 'Mi primera publicación']);
    }

    public function test_regular_users_can_not_create_or_promote_users(): void
    {
        $user = User::factory()->create(['rol' => Role::USER]);

        $this->actingAs($user)->post('/admin/users', [
            'name' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'password',
            'rol' => Role::ADMIN,
        ])->assertForbidden();

        $this->actingAs($user)->put("/admin/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'rol' => Role::ADMIN,
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
        $this->assertSame(Role::USER, (int) $user->fresh()->rol);
    }

    public function test_editors_manage_content_but_not_people(): void
    {
        $this->actingAs(User::factory()->create(['rol' => Role::EDITOR]));

        foreach ([...self::EVERYONE, ...self::CONTENT_MANAGERS] as $uri) {
            $this->get($uri)->assertOk();
        }

        foreach (self::ADMIN_ONLY as $uri) {
            $this->get($uri)->assertForbidden();
        }
    }

    public function test_the_menu_only_shows_allowed_sections(): void
    {
        $this->actingAs(User::factory()->create(['rol' => Role::USER]))
            ->get('/dashboard')
            ->assertSee('Mis publicaciones')
            ->assertSee('Crear publicación')
            ->assertDontSee('Control de usuarios')
            ->assertDontSee('Papelera de reciclaje')
            ->assertDontSee('Estadísticas');
    }

    public function test_new_registrations_are_regular_users(): void
    {
        $this->post('/register', [
            'name' => 'Nueva Persona',
            'email' => 'nueva@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertSame(Role::USER, (int) User::where('email', 'nueva@example.com')->value('rol'));
        $this->get('/admin/users')->assertForbidden();
    }
}
