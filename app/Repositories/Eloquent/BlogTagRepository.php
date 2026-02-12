<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog\BlogTag;
use App\Repositories\Contracts\BlogTagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BlogTagRepository extends BaseRepository implements BlogTagRepositoryInterface
{
    public function __construct(BlogTag $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all tags with post count
     */
    public function getAllWithPostCount(): Collection
    {
        return $this->model
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Find tag by slug
     */
    public function findBySlug(string $slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->first();
    }

    /**
     * Get tags with published posts
     */
    public function getTagsWithPublishedPosts(): Collection
    {
        return $this->model
            ->withCount(['posts' => function ($query) {
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
        return $this->model
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get();
    }
}
