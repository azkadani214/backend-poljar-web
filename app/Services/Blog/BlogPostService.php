<?php

namespace App\Services\Blog;

use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BlogPostService
{
    public function __construct(
        private BlogPostRepositoryInterface $blogPostRepository
    ) {}

    /**
     * Get all posts for admin
     */
    public function getAllPosts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->blogPostRepository->getAllPosts($filters, $perPage);
    }

    /**
     * Get published blog posts
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        return Cache::remember("blog_published_page_{$page}_per_{$perPage}", 300, function() use ($perPage) {
            return $this->blogPostRepository->getPublishedPosts($perPage);
        });
    }

    /**
     * Get blog post by slug
     */
    public function getPostBySlug(string $slug)
    {
        return $this->blogPostRepository->findBySlug($slug);
    }

    /**
     * Get post by ID
     */
    public function getPostById(string $id)
    {
        return $this->blogPostRepository->find($id, ['*'], ['user', 'categories', 'tags', 'seoDetail']);
    }

    /**
     * Get latest blog posts
     */
    public function getLatestPosts(int $limit = 3): Collection
    {
        return Cache::remember("blog_latest_{$limit}", 300, function() use ($limit) {
            return $this->blogPostRepository->getLatestPosts($limit);
        });
    }

    /**
     * Search blog posts
     */
    public function searchPosts(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->blogPostRepository->search($keyword, $perPage);
    }

    /**
     * Get posts by category
     */
    public function getPostsByCategory(string $categorySlug, int $perPage = 15): LengthAwarePaginator
    {
        return $this->blogPostRepository->getByCategory($categorySlug, $perPage);
    }

    /**
     * Get posts by tag
     */
    public function getPostsByTag(string $tagSlug, int $perPage = 15): LengthAwarePaginator
    {
        return $this->blogPostRepository->getByTag($tagSlug, $perPage);
    }

    /**
     * Create news post
     */
    public function createPost(array $data)
    {
        $post = $this->blogPostRepository->create($data);
        $this->clearBlogCache();
        return $post;
    }

    /**
     * Update news post
     */
    public function updatePost(string $id, array $data)
    {
        $post = $this->blogPostRepository->update($id, $data);
        $this->clearBlogCache();
        return $post;
    }

    /**
     * Delete news post
     */
    public function deletePost(string $id)
    {
        $result = $this->blogPostRepository->delete($id);
        $this->clearBlogCache();
        return $result;
    }

    /**
     * Bulk tasks
     */
    public function bulkUpdateStatus(array $ids, string $status)
    {
        $result = $this->blogPostRepository->bulkUpdateStatus($ids, $status);
        $this->clearBlogCache();
        return $result;
    }

    public function bulkDelete(array $ids)
    {
        $result = $this->blogPostRepository->bulkDelete($ids);
        $this->clearBlogCache();
        return $result;
    }

    /**
     * Get related blog posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection
    {
        return Cache::remember("blog_related_{$postId}_{$limit}", 300, function() use ($postId, $limit) {
            return $this->blogPostRepository->getRelatedPosts($postId, $limit);
        });
    }

    /**
     * Clear blog cache
     */
    public function clearBlogCache(): void
    {
        Cache::forget('blog_latest');
        // Clear related with a pattern is hard in Laravel default cache, 
        // usually tags are better but for now let it expire.
    }
    /**
     * Get blog post statistics
     */
    public function getPostStatistics(): array
    {
        return $this->blogPostRepository->getStatistics();
    }
}