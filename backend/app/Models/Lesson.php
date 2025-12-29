<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_id',
        'title',
        'type',
        'content',
        'duration',
        'is_preview',
        'order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_preview' => 'boolean',
        ];
    }

    /**
     * Get the section that owns the lesson.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the course through section.
     */
    public function course()
    {
        return $this->section->course ?? null;
    }

    /**
     * Check if lesson is a video.
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Check if lesson is a quiz.
     */
    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    /**
     * Check if lesson is an article.
     */
    public function isArticle(): bool
    {
        return $this->type === 'article';
    }

    /**
     * Get the quiz for this lesson (if type is quiz).
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Get the discussions for this lesson.
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }
}

