<?php

namespace App\Http\Resources\V1\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\User\UserResource;

class BlogPostDetailResource extends JsonResource
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
            'cover_photo_path' => $this->cover_photo_path,
            'photo_alt_text' => $this->photo_alt_text,
            
            // Author
            'author' => new UserResource($this->whenLoaded('user')),
            
            // Categories & Tags
            'categories' => $this->whenLoaded('categories', function() {
                return $this->categories->map(fn($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ]);
            }),
            'tags' => $this->whenLoaded('tags', function() {
                return $this->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]);
            }),
            
            // SEO
            'seo' => $this->whenLoaded('seoDetail', function($seo) {
                if (!$seo) return null;
                return [
                    'meta_title' => $seo->meta_title,
                    'meta_description' => $seo->meta_description,
                    'keywords' => $seo->keywords,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}