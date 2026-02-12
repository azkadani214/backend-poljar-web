<?php

namespace App\Http\Resources\V1\News;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\User\UserResource;

class NewsCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'approved' => $this->approved,
            'approved_at' => $this->approved_at?->toIso8601String(),
            
            // Author info (from User or Guest fields)
            'author_name' => $this->author_name,
            'name' => $this->name,
            'email' => $this->email,
            'user' => new UserResource($this->whenLoaded('user')),
            
            // Post info (when loaded)
            'post' => $this->whenLoaded('post', function() {
                return [
                    'id' => $this->post->id,
                    'title' => $this->post->title,
                    'slug' => $this->post->slug,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
