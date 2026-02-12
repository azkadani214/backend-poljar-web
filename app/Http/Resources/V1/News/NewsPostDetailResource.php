<?php

namespace App\Http\Resources\V1\News;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\User\UserResource;

class NewsPostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'sub_title' => $this->sub_title,
            'body' => $this->body,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'scheduled_for' => $this->scheduled_for?->toIso8601String(),
            'cover_photo_path' => $this->cover_photo_path,
            'cover_photo_url' => $this->cover_photo_url,
            'photo_alt_text' => $this->photo_alt_text,
            'views' => $this->views,
            'read_time' => $this->read_time,
            'is_featured' => $this->is_featured,
            
            // Author
            'author' => new UserResource($this->whenLoaded('user')),
            
            // Relationships
            'categories' => NewsCategoryResource::collection($this->whenLoaded('categories')),
            'tags' => NewsTagResource::collection($this->whenLoaded('tags')),
            'comments' => NewsCommentResource::collection($this->whenLoaded('approvedComments')),
            
            // SEO
            'seo' => $this->whenLoaded('seoDetail', function($seo) {
                if (!$seo) return null;
                return [
                    'meta_title' => $seo->meta_title,
                    'meta_description' => $seo->meta_description,
                    'keywords' => $seo->keywords,
                ];
            }),
            
            // Statistics
            'statistics' => [
                'total_comments' => $this->whenCounted('comments'),
                'approved_comments' => $this->whenCounted('approvedComments'),
                'pending_comments' => $this->whenCounted('comments', function($count) {
                    // This one is tricky with whenCounted, but better than a raw query
                    return $count; 
                }),
            ],
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
