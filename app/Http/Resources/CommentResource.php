<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'content' => $this->content,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            
            // User information
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            
            // Nested replies
            'replies' => $this->when(
                $this->relationLoaded('replies'),
                fn () => CommentResource::collection($this->replies)
            ),
            
            // Reply count
            'replies_count' => $this->when(
                isset($this->replies_count),
                $this->replies_count
            ),
            
            // Check if user can edit/delete
            'can_edit' => $this->when(
                $request->user(),
                fn () => $request->user()->id === $this->user_id
            ),
            'can_delete' => $this->when(
                $request->user(),
                fn () => $request->user()->id === $this->user_id || $request->user()->is_admin
            ),
        ];
    }
}
