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

class MyPublicationsTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['Administrador', 'Editor', 'Usuario'] as $name) {
            Role::create(['name' => $name]);
        }

        $this->category = Category::create(['name' => 'Deportes']);
    }

    public function test_users_only_see_their_own_publications(): void
    {
        $author = User::factory()->create(['rol' => Role::USER]);
        $this->publicationBy($author, 'Mi historia');
        $this->publicationBy(User::factory()->create(), 'Historia ajena');

        $this->actingAs($author)->get('/admin/publications/mine')
            ->assertOk()
            ->assertSee('Mi historia')
            ->assertDontSee('Historia ajena')
            ->assertSee(route('admin.publications.edit', Publication::where('title', 'Mi historia')->first()));
    }

    public function test_the_dashboard_card_links_regular_users_to_their_publications(): void
    {
        $this->actingAs(User::factory()->create(['rol' => Role::USER]))
            ->get('/dashboard')
            ->assertSee(route('admin.publications.mine'));
    }

    public function test_authors_can_edit_their_publication_and_keep_the_image(): void
    {
        $author = User::factory()->create(['rol' => Role::USER]);
        $publication = $this->publicationBy($author, 'Título viejo');

        $this->actingAs($author)->get("/admin/publications/{$publication->id}/edit")->assertOk()->assertSee('Título viejo');

        $this->actingAs($author)->put("/admin/publications/{$publication->id}", [
            'title' => 'Título nuevo',
            'category_id' => $this->category->id,
            'content' => 'Contenido nuevo',
        ])->assertRedirect('/admin/publications/mine');

        $publication->refresh();
        $this->assertSame('Título nuevo', $publication->title);
        $this->assertSame('titulo-nuevo', $publication->slug);
        $this->assertSame('https://example.com/image.jpg', $publication->src_img);
    }

    public function test_a_new_image_replaces_the_previous_one(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['rol' => Role::USER]);
        $publication = $this->publicationBy($author, 'Con foto');
        $publication->update(['src_img' => UploadedFile::fake()->create('old.jpg', 10, 'image/jpeg')->store('publications', 'public')]);
        $oldImage = $publication->src_img;

        $this->actingAs($author)->put("/admin/publications/{$publication->id}", [
            'title' => 'Con foto',
            'category_id' => $this->category->id,
            'content' => 'Contenido',
            'src_img' => UploadedFile::fake()->create('new.jpg', 10, 'image/jpeg'),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($publication->fresh()->src_img);
    }

    public function test_users_can_not_edit_publications_of_others(): void
    {
        $publication = $this->publicationBy(User::factory()->create(), 'Ajena');
        $intruder = User::factory()->create(['rol' => Role::USER]);

        $this->actingAs($intruder)->get("/admin/publications/{$publication->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->put("/admin/publications/{$publication->id}", [
            'title' => 'Hackeada',
            'category_id' => $this->category->id,
            'content' => 'x',
        ])->assertForbidden();

        $this->assertSame('Ajena', $publication->fresh()->title);
    }

    public function test_editors_can_edit_any_publication(): void
    {
        $publication = $this->publicationBy(User::factory()->create(), 'Original');

        $this->actingAs(User::factory()->create(['rol' => Role::EDITOR]))->put("/admin/publications/{$publication->id}", [
            'title' => 'Corregida por el editor',
            'category_id' => $this->category->id,
            'content' => 'Contenido',
        ])->assertRedirect('/admin/publications');

        $this->assertSame('Corregida por el editor', $publication->fresh()->title);
    }

    private function publicationBy(User $user, string $title): Publication
    {
        return Publication::create([
            'title' => $title,
            'slug' => str($title)->slug(),
            'src_img' => 'https://example.com/image.jpg',
            'user_id' => $user->id,
            'category_id' => $this->category->id,
            'status' => Publication::STATUS_PUBLISHED,
            'content' => 'Contenido',
        ]);
    }
}
