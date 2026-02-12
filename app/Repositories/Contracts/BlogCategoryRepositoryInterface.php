<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogCategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all categories with post count
     */
    public function getAllWithPostCount(): Collection;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug);

    /**
     * Get categories with published posts
     */
    public function getCategoriesWithPublishedPosts(): Collection;

    /**
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 10): Collection;
}
