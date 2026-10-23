<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['Administrador', 'Editor', 'Usuario'] as $name) {
            Role::create(['name' => $name]);
        }

        $this->admin = User::factory()->create(['rol' => Role::ADMIN]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/users')->assertRedirect('/login');
    }

    public function test_admin_pages_render(): void
    {
        $category = Category::create(['name' => 'Deportes']);
        Publication::create([
            'title' => 'Primera',
            'slug' => 'primera',
            'src_img' => 'https://example.com/image.jpg',
            'user_id' => $this->admin->id,
            'category_id' => $category->id,
            'status' => Publication::STATUS_PUBLISHED,
            'content' => 'Contenido',
        ]);

        $this->actingAs($this->admin);

        foreach (['/dashboard', '/blog', '/admin/users', '/admin/users/hidden', '/admin/publications', '/admin/publications/create', '/admin/categories', '/admin/roles', '/admin/statistics', '/admin/recycle-bin'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_blog_latest_section_starts_with_the_newest_publication(): void
    {
        $category = Category::create(['name' => 'Deportes']);

        foreach (['Publicación antigua' => now()->subDay(), 'Publicación nueva' => now()] as $title => $date) {
            $publication = Publication::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'src_img' => 'https://example.com/image.jpg',
                'user_id' => $this->admin->id,
                'category_id' => $category->id,
                'status' => Publication::STATUS_PUBLISHED,
                'content' => 'Contenido',
            ]);
            $publication->forceFill(['created_at' => $date])->save();
        }

        $this->actingAs($this->admin)->get('/blog')
            ->assertOk()
            ->assertViewHas('latest', fn ($latest) => $latest->pluck('title')->all() === ['Publicación nueva', 'Publicación antigua'])
            ->assertSee('Más reciente');
    }

    public function test_dashboard_greeting_has_no_date_or_emoji(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')
            ->assertOk()
            ->assertDontSee('👋')
            ->assertDontSee(now()->locale('es')->isoFormat('dddd D [de] MMMM'));
    }

    public function test_category_and_role_names_must_be_unique(): void
    {
        $this->actingAs($this->admin);
        $category = Category::create(['name' => 'Deportes']);
        Category::create(['name' => 'Arte'])->delete();

        $this->post('/admin/categories', ['name' => 'Deportes'])->assertSessionHasErrors('name');
        $this->post('/admin/categories', ['name' => 'Arte'])->assertSessionHasErrors('name');
        $this->put("/admin/categories/{$category->id}", ['name' => 'Deportes'])->assertSessionHasNoErrors();
        $this->post('/admin/roles', ['name' => 'Editor'])->assertSessionHasErrors('name');

        $this->assertSame(1, Category::where('name', 'Deportes')->count());
    }

    public function test_seeders_can_run_twice_without_duplicates(): void
    {
        $this->seed([RoleSeeder::class, CategorySeeder::class]);
        $this->seed([RoleSeeder::class, CategorySeeder::class]);

        $this->assertSame(3, Role::withTrashed()->count());
        $this->assertSame(16, Category::withTrashed()->count());
    }

    public function test_users_can_be_searched_by_name_or_email(): void
    {
        User::factory()->create(['name' => 'Ana López', 'email' => 'ana@example.com']);
        User::factory()->create(['name' => 'Bruno Díaz', 'email' => 'bruno@example.com']);

        $this->actingAs($this->admin);

        $this->get('/admin/users?search=ana')->assertOk()->assertSee('Ana López')->assertDontSee('Bruno Díaz');
        $this->get('/admin/users?search=bruno@')->assertOk()->assertSee('Bruno Díaz')->assertDontSee('Ana López');
    }

    public function test_profile_page_uses_the_admin_layout(): void
    {
        $this->actingAs($this->admin)->get('/user/profile')->assertOk()->assertSee('Mi perfil');
    }

    public function test_delete_routes_reject_get_requests(): void
    {
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs($this->admin)
            ->get("/admin/categories/{$category->id}")
            ->assertMethodNotAllowed();

        $this->assertNotSoftDeleted($category);
    }

    public function test_category_can_be_deleted_and_restored(): void
    {
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}")->assertSessionHas('success');
        $this->assertSoftDeleted($category);

        $this->patch("/admin/categories/{$category->id}/restore")->assertSessionHas('success');
        $this->assertNotSoftDeleted($category);
    }

    public function test_category_with_publications_is_not_deleted(): void
    {
        $category = Category::create(['name' => 'Deportes']);
        $this->admin->publications()->create([
            'title' => 'Primera',
            'slug' => 'primera',
            'src_img' => 'image.jpg',
            'category_id' => $category->id,
            'status' => Publication::STATUS_PUBLISHED,
            'content' => 'Contenido',
        ]);

        $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}")->assertSessionHas('failed');

        $this->assertNotSoftDeleted($category);
    }

    public function test_user_can_be_created_updated_deleted_and_restored(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => 'password123',
            'rol' => Role::EDITOR,
        ])->assertSessionHas('success');

        $user = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertSame(Role::EDITOR, (int) $user->rol);

        $this->put("/admin/users/{$user->id}", [
            'name' => 'Editor 2',
            'email' => 'editor@example.com',
            'rol' => Role::USER,
        ])->assertSessionHas('success');
        $this->assertSame('Editor 2', $user->fresh()->name);

        $this->delete("/admin/users/{$user->id}")->assertSessionHas('success');
        $this->assertSoftDeleted($user);

        $this->patch("/admin/users/{$user->id}/restore")->assertSessionHas('success');
        $this->assertNotSoftDeleted($user);
    }

    public function test_publication_can_be_created(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs($this->admin)->post('/admin/publications', [
            'title' => 'Nueva publicación',
            'category_id' => $category->id,
            'content' => 'Contenido',
            'src_img' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/admin/publications/mine');

        $publication = Publication::firstOrFail();
        $this->assertSame('nueva-publicacion', $publication->slug);
        Storage::disk('public')->assertExists($publication->src_img);
    }

    public function test_publication_images_up_to_five_megabytes_are_accepted(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs($this->admin)->post('/admin/publications', [
            'title' => 'Foto grande',
            'category_id' => $category->id,
            'content' => 'Contenido',
            'src_img' => UploadedFile::fake()->create('photo.jpg', 4500, 'image/jpeg'),
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('publications', 1);
    }

    public function test_too_large_publication_images_are_rejected_in_spanish(): void
    {
        $category = Category::create(['name' => 'Deportes']);

        $this->actingAs($this->admin)->post('/admin/publications', [
            'title' => 'Foto enorme',
            'category_id' => $category->id,
            'content' => 'Contenido',
            'src_img' => UploadedFile::fake()->create('photo.jpg', 6000, 'image/jpeg'),
        ])->assertSessionHasErrors(['src_img' => 'La imagen pesa demasiado: el máximo es 5 MB.']);

        $this->assertDatabaseCount('publications', 0);
    }
}
