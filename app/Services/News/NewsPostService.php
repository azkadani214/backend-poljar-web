<?php
// ============================================================================
// FILE 55: app/Services/News/NewsPostService.php
// ============================================================================

namespace App\Services\News;

use App\Models\News\NewsPost;
use App\Repositories\Contracts\NewsPostRepositoryInterface;
use App\Repositories\Contracts\NewsCategoryRepositoryInterface;
use App\Repositories\Contracts\NewsTagRepositoryInterface;
use App\Services\Upload\ImageUploadService;
use App\Services\News\NewsSeoService;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class NewsPostService
{
    public function __construct(
        private NewsPostRepositoryInterface $newsPostRepository,
        private NewsCategoryRepositoryInterface $newsCategoryRepository,
        private NewsTagRepositoryInterface $newsTagRepository,
        private ImageUploadService $imageUploadService,
        private NewsSeoService $newsSeoService
    ) {}

    /**
     * Get all posts with filters
     */
    public function getAllPosts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->newsPostRepository->getAllPostsAdmin($filters, $perPage);
    }

    /**
     * Get published posts (for public)
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        return Cache::remember("news_published_page_{$page}_per_{$perPage}", 300, function() use ($perPage) {
            return $this->newsPostRepository->getPublishedPosts($perPage);
        });
    }

    /**
     * Get featured posts
     */
    public function getFeaturedPosts(int $limit = 3): Collection
    {
        return Cache::remember("news_featured_{$limit}", 300, function() use ($limit) {
            return $this->newsPostRepository->getFeaturedPosts($limit);
        });
    }

    /**
     * Get post by ID
     */
    public function getPostById(string $id): NewsPost
    {
        $post = $this->newsPostRepository->find(
            $id,
            ['*'],
            ['user', 'categories', 'tags', 'seoDetail', 'approvedComments.user']
        );

        if (!$post) {
            throw new NotFoundException('News post not found');
        }

        return $post;
    }

    /**
     * Get post by slug
     */
    public function getPostBySlug(string $slug): NewsPost
    {
        $post = $this->newsPostRepository->findBySlug($slug);

        if (!$post) {
            throw new NotFoundException('News post not found');
        }

        // Increment views
        $this->newsPostRepository->incrementViews($post->id);

        return $post;
    }

    /**
     * Create news post
     */
    public function createPost(array $data): NewsPost
    {
        DB::beginTransaction();

        try {
            // Handle cover photo upload
            if (isset($data['cover_photo'])) {
                $uploadResult = $this->imageUploadService->uploadCoverImage(
                    $data['cover_photo'],
                    'news/covers'
                );
                $data['cover_photo_path'] = $uploadResult['path'];
            }

            // Calculate read time
            $data['read_time'] = $this->calculateReadTime($data['body']);

            // Set default status
            $data['status'] = $data['status'] ?? 'draft';

            // Set published_at if publishing immediately
            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            // Create post
            $post = $this->newsPostRepository->create($data);

            // Clear cache
            $this->clearNewsCache();

            // Attach categories
            if (isset($data['categories']) && !empty($data['categories'])) {
                $post->categories()->sync($data['categories']);
            }

            // Attach or create tags
            if (isset($data['tags']) && !empty($data['tags'])) {
                $tagIds = $this->getOrCreateTags($data['tags']);
                $post->tags()->sync($tagIds);
            }

            // Create SEO details
            if (isset($data['seo'])) {
                $this->newsSeoService->createOrUpdateSeo($post->id, $data['seo']);
            }

            DB::commit();

            return $post->load(['user', 'categories', 'tags', 'seoDetail']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded cover photo if error
            if (isset($data['cover_photo_path'])) {
                $this->imageUploadService->delete($data['cover_photo_path']);
            }

            throw $e;
        }
    }

    /**
     * Update news post
     */
    public function updatePost(string $id, array $data): NewsPost
    {
        DB::beginTransaction();

        try {
            $post = $this->getPostById($id);

            // Handle cover photo upload
            if (isset($data['cover_photo'])) {
                // Delete old cover photo
                if ($post->cover_photo_path) {
                    $this->imageUploadService->delete($post->cover_photo_path);
                }

                // Upload new cover photo
                $uploadResult = $this->imageUploadService->uploadCoverImage(
                    $data['cover_photo'],
                    'news/covers'
                );
                $data['cover_photo_path'] = $uploadResult['path'];
            }

            // Update slug if title changed
            if (isset($data['title']) && $data['title'] !== $post->title && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Recalculate read time if body changed
            if (isset($data['body'])) {
                $data['read_time'] = $this->calculateReadTime($data['body']);
            }

            // Update post
            $this->newsPostRepository->update($id, $data);

            // Clear cache
            $this->clearNewsCache();

            // Update categories
            if (isset($data['categories'])) {
                $post->categories()->sync($data['categories']);
            }

            // Update tags
            if (isset($data['tags'])) {
                $tagIds = $this->getOrCreateTags($data['tags']);
                $post->tags()->sync($tagIds);
            }

            // Update SEO details
            if (isset($data['seo'])) {
                $this->newsSeoService->createOrUpdateSeo($post->id, $data['seo']);
            }

            DB::commit();

            return $post->fresh(['user', 'categories', 'tags', 'seoDetail']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded cover photo if error
            if (isset($data['cover_photo_path']) && is_string($data['cover_photo_path'])) {
                $this->imageUploadService->delete($data['cover_photo_path']);
            }

            throw $e;
        }
    }

    /**
     * Delete news post
     */
    public function deletePost(string $id): bool
    {
        DB::beginTransaction();

        try {
            $post = $this->getPostById($id);

            // Delete cover photo
            if ($post->cover_photo_path) {
                $this->imageUploadService->delete($post->cover_photo_path);
            }

            // Delete post (will cascade delete comments and SEO)
            $result = $this->newsPostRepository->delete($id);

            // Clear cache
            $this->clearNewsCache();

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Publish post
     */
    public function publishPost(string $id): NewsPost
    {
        $post = $this->getPostById($id);

        if ($post->status === 'published') {
            throw new ValidationException('Post is already published');
        }

        $this->newsPostRepository->publish($id);
        $this->clearNewsCache();

        return $post->fresh();
    }

    /**
     * Unpublish post
     */
    public function unpublishPost(string $id): NewsPost
    {
        $post = $this->getPostById($id);

        if ($post->status !== 'published') {
            throw new ValidationException('Post is not published');
        }

        $this->newsPostRepository->unpublish($id);
        $this->clearNewsCache();

        return $post->fresh();
    }

    /**
     * Schedule post
     */
    public function schedulePost(string $id, \DateTime $dateTime): NewsPost
    {
        $this->getPostById($id); // Check if exists

        if ($dateTime <= now()) {
            throw new ValidationException('Schedule time must be in the future');
        }

        $this->newsPostRepository->schedule($id, $dateTime);

        return $this->getPostById($id);
    }

    /**
     * Feature post
     */
    public function featurePost(string $id): NewsPost
    {
        $this->newsPostRepository->feature($id);
        $this->clearNewsCache();

        return $this->getPostById($id);
    }

    /**
     * Unfeature post
     */
    public function unfeaturePost(string $id): NewsPost
    {
        $this->newsPostRepository->unfeature($id);
        $this->clearNewsCache();
        return $this->getPostById($id);
    }

    /**
     * Clear news related cache
     */
    private function clearNewsCache(): void
    {
        // Using tags would be better, but default file/database driver doesn't support them
        // So we flush or use a pattern if possible, but let's clear the specific keys
        // or just clear all news prefix if we had a better way. 
        // For now, let's at least clear the common ones.
        // A better way is to use Cache::forget with known keys or use a custom flush logic.
        
        // Simple approach: flush if we don't have tags support. 
        // But better to just forget most common ones.
        Cache::forget('news_featured');
        Cache::forget('news_latest');
        Cache::forget('news_popular');
        
        // Clearing paginated cache is tricky without tags. 
        // We might want to use a versioned key or just clear everything if it's not too heavy.
        // For this project, clearing everything news related is fine.
    }

    /**
     * Search posts
     */
    public function searchPosts(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->newsPostRepository->search($keyword, $perPage);
    }

    /**
     * Get latest posts
     */
    public function getLatestPosts(int $limit = 5): Collection
    {
        return Cache::remember("news_latest_{$limit}", 300, function() use ($limit) {
            return $this->newsPostRepository->getLatestPosts($limit);
        });
    }

    /**
     * Get popular posts
     */
    public function getPopularPosts(int $limit = 5): Collection
    {
        return Cache::remember("news_popular_{$limit}", 300, function() use ($limit) {
            return $this->newsPostRepository->getPopularPosts($limit);
        });
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection
    {
        return $this->newsPostRepository->getRelatedPosts($postId, $limit);
    }

    /**
     * Get or create tags
     */
    private function getOrCreateTags(array $tagData): array
    {
        $tagIds = [];

        foreach ($tagData as $tagInput) {
            if (is_string($tagInput)) {
                // Create new tag
                $tag = $this->newsTagRepository->findOrCreateByName($tagInput);
                $tagIds[] = $tag->id;
            } else {
                // Existing tag ID
                $tagIds[] = $tagInput;
            }
        }

        return $tagIds;
    }

    /**
     * Calculate read time based on word count
     */
    private function calculateReadTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200); // Average reading speed: 200 words/min

        return $minutes . ' min';
    }

    /**
     * Get post statistics
     */
    public function getPostStatistics(): array
    {
        return [
            'total' => $this->newsPostRepository->count(),
            'published' => $this->newsPostRepository->count(['status' => 'published']),
            'draft' => $this->newsPostRepository->count(['status' => 'draft']),
            'scheduled' => $this->newsPostRepository->count(['status' => 'scheduled']),
        ];
    }
}