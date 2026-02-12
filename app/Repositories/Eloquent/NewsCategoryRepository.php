<?php

namespace App\Repositories\Eloquent;

use App\Models\News\NewsCategory;
use App\Repositories\Contracts\NewsCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NewsCategoryRepository extends BaseRepository implements NewsCategoryRepositoryInterface
{
    public function __construct(NewsCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get categories with posts count
     */
    public function getCategoriesWithPostsCount(): Collection
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
    public function findBySlug(string $slug): ?NewsCategory
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