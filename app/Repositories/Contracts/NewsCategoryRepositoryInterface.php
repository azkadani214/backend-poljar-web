<?php

namespace App\Repositories\Contracts;

use App\Models\News\NewsCategory;
use Illuminate\Database\Eloquent\Collection;

interface NewsCategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get categories with posts count
     */
    public function getCategoriesWithPostsCount(): Collection;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?NewsCategory;

    /**
     * Get categories with published posts
     */
    public function getCategoriesWithPublishedPosts(): Collection;

    /**
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 10): Collection;
}