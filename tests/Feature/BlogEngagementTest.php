<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use App\Support\CoverImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogEngagementTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private User $author;

    private User $reader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['Administrador', 'Editor', 'Usuario'] as $name) {
            Role::create(['name' => $name]);
        }

        $this->category = Category::create(['name' => 'Tecnología']);
        $this->author = User::factory()->create(['rol' => Role::USER, 'name' => 'Ana Autora']);
        $this->reader = User::factory()->create(['rol' => Role::USER, 'name' => 'Luis Lector']);
    }

    private function publication(string $title = 'Una gran historia', ?User $author = null, ?Category $category = null): Publication
    {
        return ($author ?? $this->author)->publications()->create([
            'title' => $title,
            'slug' => Str::slug($title),
            'src_img' => 'https://example.com/image.jpg',
            'category_id' => ($category ?? $this->category)->id,
            'status' => Publication::STATUS_PUBLISHED,
            'content' => 'Contenido de '.$title,
        ]);
    }

    public function test_cards_link_to_the_publication_page(): void
    {
        $publication = $this->publication();

        $this->actingAs($this->reader)->get('/blog')
            ->assertOk()
            ->assertSee($publication->url(), false);

        $this->assertSame(route('blog.show', [$publication->id, 'una-gran-historia']), $publication->url());
    }

    public function test_the_publication_page_shows_content_comments_and_related_reading(): void
    {
        $publication = $this->publication();
        $related = $this->publication('Otra del mismo tema');
        $this->publication('De otro tema', category: Category::create(['name' => 'Cocina']));
        $publication->comments()->create(['user_id' => $this->reader->id, 'comment' => '¡Me encantó!']);

        $this->actingAs($this->reader)->get($publication->url())
            ->assertOk()
            ->assertSee('Una gran historia')
            ->assertSee('¡Me encantó!')
            ->assertSee('Sigue leyendo en Tecnología')
            ->assertSee($related->title)
            ->assertDontSee('De otro tema')
            ->assertSee('Reportar');
    }

    public function test_a_wrong_slug_redirects_to_the_real_address(): void
    {
        $publication = $this->publication();

        $this->actingAs($this->reader)->get("/blog/{$publication->id}/otro-titulo")
            ->assertRedirect($publication->url());
        $this->actingAs($this->reader)->get("/blog/{$publication->id}")
            ->assertRedirect($publication->url());
    }

    public function test_removed_publications_are_not_found(): void
    {
        $publication = $this->publication();
        $publication->delete();

        $this->actingAs($this->reader)->get($publication->url())->assertNotFound();
    }

    public function test_one_reaction_per_reader_that_can_change_or_be_removed(): void
    {
        $publication = $this->publication();
        $url = route('blog.reaction', $publication);

        $this->actingAs($this->reader)->postJson($url, ['type' => 'love'])
            ->assertOk()->assertJson(['mine' => 'love', 'counts' => ['love' => 1, 'fire' => 0]]);

        $this->actingAs($this->reader)->postJson($url, ['type' => 'fire'])
            ->assertOk()->assertJson(['mine' => 'fire', 'counts' => ['love' => 0, 'fire' => 1]]);
        $this->assertDatabaseCount('reaction', 1);

        $this->actingAs($this->reader)->postJson($url, ['type' => 'fire'])
            ->assertOk()->assertJson(['mine' => null, 'counts' => ['fire' => 0]]);
        $this->assertDatabaseCount('reaction', 0);

        $this->actingAs($this->reader)->postJson($url, ['type' => 'boring'])->assertUnprocessable();
    }

    public function test_reactions_work_without_javascript(): void
    {
        $publication = $this->publication();

        $this->actingAs($this->reader)->from($publication->url())
            ->post(route('blog.reaction', $publication), ['type' => 'idea'])
            ->assertRedirect($publication->url());

        $this->assertDatabaseHas('reaction', ['user_id' => $this->reader->id, 'type_reaction' => 'idea']);
    }

    public function test_saving_toggles_and_fills_the_saved_page(): void
    {
        $publication = $this->publication();
        $url = route('blog.save', $publication);

        $this->actingAs($this->reader)->postJson($url)->assertOk()->assertJson(['saved' => true]);
        $this->actingAs($this->reader)->get('/blog/guardados')->assertOk()->assertSee('Una gran historia');

        $this->actingAs($this->reader)->postJson($url)->assertOk()->assertJson(['saved' => false]);
        $this->actingAs($this->reader)->get('/blog/guardados')->assertOk()->assertSee('Aún no guardas nada');
    }

    public function test_saved_pages_belong_to_each_reader(): void
    {
        $publication = $this->publication();
        $this->actingAs($this->author)->postJson(route('blog.save', $publication));

        $this->actingAs($this->reader)->get('/blog/guardados')->assertDontSee('Una gran historia');
    }

    public function test_a_reader_reports_a_publication_once(): void
    {
        $publication = $this->publication();
        $url = route('blog.report', $publication);

        $this->actingAs($this->reader)->post($url, ['reason' => 'spam', 'description' => 'Puro anuncio'])
            ->assertSessionHas('success', 'Gracias, el equipo revisará esta publicación.');
        $this->actingAs($this->reader)->post($url, ['reason' => 'other'])
            ->assertSessionHas('success', 'Ya habías reportado esta publicación.');

        $this->assertDatabaseCount('complaint', 1);
        $this->assertDatabaseHas('complaint', ['publication_id' => $publication->id, 'reason' => 'spam', 'status' => 'pending']);

        $this->actingAs($this->reader)->get($publication->url())->assertSee('Reportada');
    }

    public function test_reports_need_a_known_reason_and_authors_cannot_report_themselves(): void
    {
        $publication = $this->publication();

        $this->actingAs($this->reader)->post(route('blog.report', $publication), ['reason' => 'boring'])
            ->assertSessionHasErrors('reason');
        $this->actingAs($this->author)->post(route('blog.report', $publication), ['reason' => 'spam'])
            ->assertSessionHas('failed');

        $this->assertDatabaseCount('complaint', 0);
    }

    public function test_only_staff_can_open_the_moderation_queue(): void
    {
        $this->actingAs($this->reader)->get('/admin/reports')->assertForbidden();

        $editor = User::factory()->create(['rol' => Role::EDITOR]);
        $this->actingAs($editor)->get('/admin/reports')->assertOk()->assertSee('Sin reportes pendientes');
    }

    public function test_staff_can_dismiss_resolve_or_take_down_and_restore(): void
    {
        $admin = User::factory()->create(['rol' => Role::ADMIN]);
        $publication = $this->publication('Publicación polémica');
        $other = User::factory()->create(['rol' => Role::USER]);

        $first = Complaint::create(['user_id' => $this->reader->id, 'publication_id' => $publication->id, 'reason' => 'harassment', 'description' => '', 'status' => 'pending']);
        $second = Complaint::create(['user_id' => $other->id, 'publication_id' => $publication->id, 'reason' => 'spam', 'description' => '', 'status' => 'pending']);

        $this->actingAs($admin)->get('/admin/reports')
            ->assertOk()
            ->assertSee('Publicación polémica')
            ->assertSee('Acoso o lenguaje de odio')
            ->assertSee('2 reportes');

        $this->actingAs($admin)->patch(route('admin.reports.update', $first), ['action' => 'dismiss'])->assertSessionHas('success');
        $this->assertSame('dismissed', $first->fresh()->status);
        $this->assertSame($admin->id, $first->fresh()->resolved_by);

        $this->actingAs($admin)->patch(route('admin.reports.update', $second), ['action' => 'remove'])->assertSessionHas('success');
        $this->assertSoftDeleted($publication);
        $this->assertSame('resolved', $second->fresh()->status);

        $this->actingAs($admin)->get('/admin/reports?estado=resolved')->assertSee('Restaurar publicación');
        $this->actingAs($admin)->patch(route('admin.reports.restore-publication', $second))->assertSessionHas('success');
        $this->assertNotSoftDeleted($publication);
    }

    public function test_search_finds_by_title_content_or_author(): void
    {
        $this->publication('Aprendiendo Kotlin');
        $this->publication('Recetas de cocina', $this->reader);

        $this->actingAs($this->reader)->get('/blog?q=kotlin')
            ->assertOk()
            ->assertSee('Resultados para “kotlin”', false)
            ->assertSee('Aprendiendo Kotlin')
            ->assertDontSee('Recetas de cocina');

        $this->actingAs($this->reader)->get('/blog?q=Luis')->assertSee('Recetas de cocina')->assertDontSee('Aprendiendo Kotlin');
        $this->actingAs($this->reader)->get('/blog?q=zzzz')->assertSee('No encontramos publicaciones');
    }

    public function test_category_chips_filter_the_list(): void
    {
        $cooking = Category::create(['name' => 'Cocina']);
        $this->publication('Aprendiendo Kotlin');
        $this->publication('Recetas de cocina', category: $cooking);

        $this->actingAs($this->reader)->get('/blog?categoria='.$cooking->id)
            ->assertOk()
            ->assertSee('Recetas de cocina')
            ->assertDontSee('Aprendiendo Kotlin');
    }

    public function test_the_list_is_paginated(): void
    {
        foreach (range(1, 12) as $i) {
            $this->publication(sprintf('Historia %02d', $i))
                ->forceFill(['created_at' => now()->subMinutes(12 - $i)])->save();
        }

        // 10 per page, newest first: the second page holds the two oldest
        $this->actingAs($this->reader)->get('/blog?page=2')
            ->assertOk()
            ->assertSee('Historia 01')
            ->assertSee('Historia 02')
            ->assertDontSee('Historia 12');
    }

    public function test_big_covers_are_scaled_down_before_being_stored(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD is not installed.');
        }

        Storage::fake('public');
        $path = CoverImage::store(UploadedFile::fake()->image('foto.jpg', 4000, 3000));

        Storage::disk('public')->assertExists($path);
        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(CoverImage::MAX_SIDE, $width);
        $this->assertSame(1200, $height);
        $this->assertStringEndsWith('.jpg', $path);
    }

    public function test_small_covers_and_non_images_are_kept_as_uploaded(): void
    {
        Storage::fake('public');

        $gif = CoverImage::store(UploadedFile::fake()->create('anim.gif', 10, 'image/gif'));
        Storage::disk('public')->assertExists($gif);
        $this->assertStringStartsWith('publications/', $gif);
    }
}
