<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        ])->assertRedirect('/admin/publications');

        $publication = Publication::firstOrFail();
        $this->assertSame('nueva-publicacion', $publication->slug);
        Storage::disk('public')->assertExists($publication->src_img);
    }
}
