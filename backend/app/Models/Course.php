<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instructor_id',
        'title',
        'subtitle',
        'description',
        'learning_objectives',
        'category',
        'subcategory',
        'difficulty_level',
        'thumbnail',
        'price',
        'currency',
        'status',
        'published_at',
        'average_rating',
        'total_enrollments',
        'total_reviews',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'learning_objectives' => 'array',
            'price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the instructor that owns the course.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the sections for the course.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Check if course is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if course is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if course can be published.
     */
    public function canBePublished(): bool
    {
        // Must have at least 1 section with 1 video lesson
        $hasVideoLesson = $this->sections()
            ->whereHas('lessons', function ($query) {
                $query->where('type', 'video');
            })
            ->exists();

        return $hasVideoLesson && 
               !empty($this->thumbnail) && 
               !empty($this->description) &&
               strlen($this->description) >= 100;
    }
}

