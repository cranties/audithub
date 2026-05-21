<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'created_by',
        'title',
        'description',
        'schema',
        'is_locked',
        'public_token',
    ];

    protected $casts = [
        'schema'    => 'array',
        'is_locked' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Survey $model): void {
            $model->uuid = (string) Str::uuid();
        });
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Domain actions ─────────────────────────────────────────────────────

    /**
     * Generate a public token and lock the schema so it can no longer be edited.
     */
    public function publishAndLock(): void
    {
        $this->update([
            'public_token' => Str::random(64),
            'is_locked'    => true,
        ]);
    }

    /**
     * Clone this survey as a new unlocked draft (preserves the schema).
     */
    public function duplicate(): Survey
    {
        return static::create([
            'created_by'  => $this->created_by,
            'title'       => $this->title . ' (Copy)',
            'description' => $this->description,
            'schema'      => $this->schema,
            'is_locked'   => false,
            'public_token' => null,
        ]);
    }
}
