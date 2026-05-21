<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'survey_id',
        'answers',
        'pdf_path',
        'submitter_ip',
        'submitted_at',
    ];

    protected $casts = [
        'answers'      => 'array',
        'submitted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Submission $model): void {
            $model->uuid         = (string) Str::uuid();
            $model->submitted_at ??= now();
        });
    }

    // ── Relations ──────────────────────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
