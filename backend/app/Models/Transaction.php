<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
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
        'amount',
        'currency',
        'platform_fee',
        'instructor_revenue',
        'payment_method',
        'payment_gateway_id',
        'status',
        'refunded_at',
        'refund_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'instructor_revenue' => 'decimal:2',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this transaction.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Check if transaction is eligible for refund.
     */
    public function isEligibleForRefund(): bool
    {
        if (!$this->isCompleted()) {
            return false;
        }

        // Check if transaction is within 30 days
        $daysSincePurchase = now()->diffInDays($this->created_at);
        if ($daysSincePurchase > 30) {
            return false;
        }

        // Check if student has watched less than 30% of the course
        $enrollment = Enrollment::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->first();

        if (!$enrollment) {
            return false;
        }

        return $enrollment->progress_percentage < 30;
    }
}

