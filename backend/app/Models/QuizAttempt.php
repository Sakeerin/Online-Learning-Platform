<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'enrollment_id',
        'quiz_id',
        'attempt_number',
        'answers',
        'score',
        'is_passed',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'decimal:2',
            'is_passed' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns the attempt.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the quiz that was attempted.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the student who made this attempt through enrollment.
     */
    public function student()
    {
        return $this->enrollment->student ?? null;
    }

    /**
     * Check if attempt passed.
     */
    public function passed(): bool
    {
        return $this->is_passed;
    }
}

