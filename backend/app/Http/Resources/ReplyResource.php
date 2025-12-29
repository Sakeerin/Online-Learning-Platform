<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
            'discussion_id' => $this->discussion_id,
            'user_id' => $this->user_id,
            'reply_text' => $this->reply_text,
            'upvotes' => $this->upvotes,
            'is_instructor_reply' => $this->is_instructor_reply,
            'user' => new UserResource($this->whenLoaded('user')),
            'discussion' => new DiscussionResource($this->whenLoaded('discussion')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

