<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
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
            'course_id' => $this->course_id,
            'lesson_id' => $this->lesson_id,
            'user_id' => $this->user_id,
            'question' => $this->question,
            'upvotes' => $this->upvotes,
            'is_answered' => $this->is_answered,
            'user' => new UserResource($this->whenLoaded('user')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'lesson' => new LessonResource($this->whenLoaded('lesson')),
            'replies' => ReplyResource::collection($this->whenLoaded('replies')),
            'replies_count' => $this->when(isset($this->replies_count), $this->replies_count),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

