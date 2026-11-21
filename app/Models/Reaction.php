<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory;

    /**
     * The reactions a reader can leave, keyed by the value stored in "type_reaction".
     * Each reader keeps one: picking another replaces it, picking the same one removes it.
     */
    public const TYPES = [
        'love' => ['emoji' => '❤️', 'label' => 'Me encanta'],
        'fire' => ['emoji' => '🔥', 'label' => 'Increíble'],
        'idea' => ['emoji' => '💡', 'label' => 'Me hizo pensar'],
        'laugh' => ['emoji' => '😂', 'label' => 'Divertido'],
        'clap' => ['emoji' => '👏', 'label' => 'Bien hecho'],
    ];

    protected $table = 'reaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'publication_id',
        'type_reaction',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
