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

        if (! $tag) {
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

    public function createTag(array $data): Tag
    {
        return Tag::create($data);
    }

    public function updateTag(string $id, array $data): Tag
    {
        $tag = Tag::find($id);

        if (! $tag) {
            throw new \App\Exceptions\Api\NotFoundException('Blog tag not found');
        }

        $tag->update($data);

        return $tag->refresh();
    }

    public function deleteTag(string $id): bool
    {
        $tag = Tag::find($id);

        if (! $tag) {
            throw new \App\Exceptions\Api\NotFoundException('Blog tag not found');
        }

        return (bool) $tag->delete();
    }
}
