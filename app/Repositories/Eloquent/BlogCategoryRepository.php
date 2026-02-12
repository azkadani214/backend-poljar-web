<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog\BlogCategory;
use App\Repositories\Contracts\BlogCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all categories with post count
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
     * Find category by slug
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
     * Get categories with published posts
     */
    public function getCategoriesWithPublishedPosts(): Collection
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
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 10): Collection
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
