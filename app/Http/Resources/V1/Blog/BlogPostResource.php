<?php
// ============================================================================
// FILE 74: app/Http/Resources/V1/Blog/BlogPostResource.php
// ============================================================================

namespace App\Http\Resources\V1\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\User\UserResource;

class BlogPostResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'cover_photo_path' => $this->cover_photo_path,
            'photo_alt_text' => $this->photo_alt_text,
            
            // Author
            'author' => new UserResource($this->whenLoaded('user')),
            
            // Categories & Tags (assuming similar structure to News)
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
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}