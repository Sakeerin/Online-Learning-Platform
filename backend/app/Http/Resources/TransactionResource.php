<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'platform_fee' => (float) $this->platform_fee,
            'instructor_revenue' => (float) $this->instructor_revenue,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'refunded_at' => $this->refunded_at?->toISOString(),
            'refund_reason' => $this->refund_reason,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'course' => new CourseResource($this->whenLoaded('course')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

