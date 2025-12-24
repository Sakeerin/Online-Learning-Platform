<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'review_text',
        'helpful_votes',
        'instructor_response',
        'responded_at',
        'is_flagged',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'helpful_votes' => 'integer',
            'is_flagged' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Get the user (student) that wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course being reviewed.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if review has instructor response.
     */
    public function hasInstructorResponse(): bool
    {
        return !empty($this->instructor_response);
    }

    /**
     * Check if review is flagged for moderation.
     */
    public function isFlagged(): bool
    {
        return $this->is_flagged;
    }

    /**
     * Mark review as flagged.
     */
    public function flag(): void
    {
        $this->update(['is_flagged' => true]);
    }

    /**
     * Unflag review.
     */
    public function unflag(): void
    {
        $this->update(['is_flagged' => false]);
    }
}

