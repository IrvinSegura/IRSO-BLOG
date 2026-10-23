<?php

namespace App\Models;

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
     * Get the public URL of the image, which may be an external URL or a file on the public disk.
     */
    protected function srcImgUrl(): Attribute
    {
        return Attribute::get(fn () => Str::startsWith($this->src_img, ['http://', 'https://'])
            ? $this->src_img
            : asset('storage/'.$this->src_img));
    }
}
