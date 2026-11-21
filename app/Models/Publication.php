<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Publication extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_PUBLISHED = 'publicado';

    /**
     * Largest cover image accepted, in kilobytes (PHP allows up to 8 MB per upload).
     */
    public const MAX_IMAGE_KB = 5120;

    protected $table = 'publications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'src_img',
        'user_id',
        'category_id',
        'status',
        'content',
    ];

    /**
     * Get the author of the publication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the category of the publication.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    /**
     * Get the comments of the publication.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the reactions readers left on the publication.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get the saves of the publication (one per reader who kept it for later).
     */
    public function saves(): HasMany
    {
        return $this->hasMany(SavedPublication::class);
    }

    /**
     * Get the reports readers filed about the publication.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Search by title, description or author name, ignoring blank terms.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        // No escaping of % and _: LIKE escapes differ between SQLite and MySQL, and a wildcard
        // typed by a reader only widens their own search
        $like = '%'.mb_substr($term, 0, 100).'%';

        return $query->where(fn (Builder $q) => $q
            ->where('title', 'like', $like)
            ->orWhere('content', 'like', $like)
            ->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', $like)));
    }

    /**
     * Address of the public page; the slug only makes the link readable.
     */
    public function url(): string
    {
        return route('blog.show', [$this, $this->slug ?: Str::slug($this->title) ?: 'publicacion']);
    }

    /**
     * Minutes it takes to read the description, at 200 words per minute.
     */
    protected function readingMinutes(): Attribute
    {
        return Attribute::get(fn () => max(1, (int) ceil(str_word_count(strip_tags((string) $this->content)) / 200)));
    }

    /**
     * Get the public URL of the image, which may be an external URL or a file on the public disk.
     */
    protected function srcImgUrl(): Attribute
    {
        return Attribute::get(fn () => Str::startsWith($this->src_img, ['http://', 'https://'])
            ? $this->src_img
            : asset('storage/'.$this->src_img));
    }
}
