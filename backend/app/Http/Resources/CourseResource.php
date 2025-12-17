<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'learning_objectives' => $this->learning_objectives,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'difficulty_level' => $this->difficulty_level,
            'thumbnail' => $this->thumbnail,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'average_rating' => $this->average_rating ? (float) $this->average_rating : null,
            'total_enrollments' => $this->total_enrollments,
            'total_reviews' => $this->total_reviews,
            'instructor' => new UserResource($this->whenLoaded('instructor')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

