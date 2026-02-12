<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogPostRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get published blog posts
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find blog post by slug
     */
    public function findBySlug(string $slug);

    /**
     * Get latest blog posts
     */
    public function getLatestPosts(int $limit = 3): Collection;

    /**
     * Search blog posts
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts by category
     */
    public function getByCategory(string $categorySlug, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts by tag
     */
    public function getByTag(string $tagSlug, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all posts with filters (Admin)
     */
    public function getAllPosts(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get blog statistics
     */
    public function getStatistics(): array;

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, string $status): bool;

    /**
     * Bulk delete
     */
    public function bulkDelete(array $ids): bool;

    /**
     * Get related blog posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection;
}
