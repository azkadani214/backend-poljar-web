<?php

namespace App\Services\Blog;

use App\Models\Blog\BlogTag as Tag;
use Illuminate\Database\Eloquent\Collection;


class BlogTagService
{
    /**
     * Get all blog tags
     */
    public function getAllTags(): Collection
    {
        return Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published')
                ->where('published_at', '<=', now());
        }])
        ->orderBy('name')
        ->get();
    }

    /**
     * Get tag by slug
     */
    public function getTagBySlug(string $slug)
    {
        $tag = Tag::where('slug', $slug)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->first();

        if (!$tag) {
            throw new \App\Exceptions\Api\NotFoundException('Blog tag not found');
        }

        return $tag;
    }

    /**
     * Get tags with published posts
     */
    public function getTagsWithPublishedPosts(): Collection
    {
        return Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published')
                ->where('published_at', '<=', now());
        }])
        ->having('posts_count', '>', 0)
        ->orderBy('name')
        ->get();
    }

    /**
     * Get popular tags
     */
    public function getPopularTags(int $limit = 10): Collection
    {
        return Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published')
                ->where('published_at', '<=', now());
        }])
        ->having('posts_count', '>', 0)
        ->orderByDesc('posts_count')
        ->limit($limit)
        ->get();
    }
}