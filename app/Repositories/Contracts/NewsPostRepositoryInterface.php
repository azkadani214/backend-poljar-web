<?php

namespace App\Repositories\Contracts;

use App\Models\News\NewsPost;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NewsPostRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all posts for admin with filters
     */
    public function getAllPostsAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get published posts
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get draft posts
     */
    public function getDraftPosts(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get scheduled posts
     */
    public function getScheduledPosts(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get featured posts
     */
    public function getFeaturedPosts(int $limit = 3): Collection;

    /**
     * Find post by slug
     */
    public function findBySlug(string $slug): ?NewsPost;

    /**
     * Get posts by category
     */
    public function getByCategory(string $categorySlug, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts by tag
     */
    public function getByTag(string $tagSlug, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts by author
     */
    public function getByAuthor(string $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Search posts
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get latest posts
     */
    public function getLatestPosts(int $limit = 5): Collection;

    /**
     * Get popular posts
     */
    public function getPopularPosts(int $limit = 5): Collection;

    /**
     * Get related posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection;

    /**
     * Increment views
     */
    public function incrementViews(string $id): bool;

    /**
     * Publish post
     */
    public function publish(string $id): bool;

    /**
     * Unpublish post
     */
    public function unpublish(string $id): bool;

    /**
     * Schedule post
     */
    public function schedule(string $id, \DateTime $dateTime): bool;

    /**
     * Feature post
     */
    public function feature(string $id): bool;

    /**
     * Unfeature post
     */
    public function unfeature(string $id): bool;
}