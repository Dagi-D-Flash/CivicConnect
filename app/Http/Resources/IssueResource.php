<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'resolution_notes' => $this->resolution_notes,
            'resolved_at' => $this->resolved_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            
            // Relationships
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
            ],
            'department' => $this->when($this->department, [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
            ]),
            
            // Counts
            'votes_count' => $this->when(isset($this->votes_count), $this->votes_count),
            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count),
            
            // User interaction
            'has_voted' => $this->when(
                $request->user(),
                fn () => $this->votes()->where('user_id', $request->user()->id)->exists()
            ),
            
            // Photos
            'photos' => $this->when(
                $this->relationLoaded('photos'),
                fn () => $this->photos->map(fn ($photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'thumbnail_url' => $photo->thumbnail_url,
                ])
            ),
        ];
    }
}
