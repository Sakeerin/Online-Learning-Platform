<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'lesson_id',
        'user_id',
        'question',
        'upvotes',
        'is_answered',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'upvotes' => 'integer',
            'is_answered' => 'boolean',
        ];
    }

    /**
     * Get the course that owns the discussion.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lesson associated with the discussion (optional).
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the user (author) that posted the discussion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the replies for the discussion.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)->orderBy('created_at');
    }

    /**
     * Check if discussion has been answered.
     */
    public function isAnswered(): bool
    {
        return $this->is_answered;
    }

    /**
     * Mark discussion as answered.
     */
    public function markAsAnswered(): void
    {
        $this->update(['is_answered' => true]);
    }

    /**
     * Increment upvote count.
     */
    public function upvote(): void
    {
        $this->increment('upvotes');
    }

    /**
     * Decrement upvote count.
     */
    public function downvote(): void
    {
        $this->decrement('upvotes');
    }
}

