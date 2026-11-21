<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A reader's report about a publication, reviewed by administrators and editors.
 */
class Complaint extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_DISMISSED = 'dismissed';

    /**
     * Why a publication can be reported (the same reasons as the Nexus app).
     */
    public const REASONS = [
        'spam' => 'Spam o publicidad',
        'harassment' => 'Acoso o lenguaje de odio',
        'inappropriate' => 'Contenido inapropiado',
        'misinformation' => 'Información falsa',
        'other' => 'Otro motivo',
    ];

    protected $table = 'complaint';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'publication_id',
        'reason',
        'description',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class)->withTrashed();
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by')->withTrashed();
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? 'Sin motivo';
    }
}
