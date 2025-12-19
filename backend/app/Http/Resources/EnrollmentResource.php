<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'enrolled_at' => $this->enrolled_at->toISOString(),
            'last_accessed_at' => $this->last_accessed_at?->toISOString(),
            'progress_percentage' => (float) $this->progress_percentage,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at?->toISOString(),
            'course' => new CourseResource($this->whenLoaded('course')),
            'student' => new UserResource($this->whenLoaded('student')),
            'progress' => ProgressResource::collection($this->whenLoaded('progress')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

