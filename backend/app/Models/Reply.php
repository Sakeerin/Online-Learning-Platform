<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reply extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discussion_id',
        'user_id',
        'reply_text',
        'upvotes',
        'is_instructor_reply',
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
            'is_instructor_reply' => 'boolean',
        ];
    }

    /**
     * Get the discussion that owns the reply.
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    /**
     * Get the user (author) that posted the reply.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if reply is from instructor.
     */
    public function isInstructorReply(): bool
    {
        return $this->is_instructor_reply;
    }

    /**
     * Mark reply as instructor reply.
     */
    public function markAsInstructorReply(): void
    {
        $this->update(['is_instructor_reply' => true]);
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

