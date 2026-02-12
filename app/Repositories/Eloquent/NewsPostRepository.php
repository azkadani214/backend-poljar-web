<?php

namespace App\Repositories\Eloquent;

use App\Models\News\NewsPost;
use App\Repositories\Contracts\NewsPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsPostRepository extends BaseRepository implements NewsPostRepositoryInterface
{
    public function __construct(NewsPost $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all posts for admin with filters
     */
    public function getAllPostsAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['user', 'categories', 'tags']);

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category']) && $filters['category'] !== 'all') {
            $query->whereHas('categories', function($q) use ($filters) {
                // Handle both ID (UUID) and Slug
                if (\Illuminate\Support\Str::isUuid($filters['category'])) {
                    $q->where('news_categories.id', $filters['category']);
                } else {
                    $q->where('news_categories.slug', $filters['category']);
                }
            });
        }

        if (isset($filters['tag']) && $filters['tag'] !== 'all') {
            $query->whereHas('tags', function($q) use ($filters) {
                if (\Illuminate\Support\Str::isUuid($filters['tag'])) {
                    $q->where('news_tags.id', $filters['tag']);
                } else {
                    $q->where('news_tags.slug', $filters['tag']);
                }
            });
        }

        if (isset($filters['author']) && !empty($filters['author'])) {
            $query->where('user_id', $filters['author']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('sub_title', 'like', "%{$keyword}%")
                  ->orWhere('body', 'like', "%{$keyword}%")
                  ->orWhere('excerpt', 'like', "%{$keyword}%");
            });
        }

        // Default ordering for admin should be newest first (by created_at or updated_at)
        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Get published posts
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'read_time', 'views', 'published_at')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with([
                'user:id,name', 
                'categories:id,name,slug,color', 
                'tags:id,name,slug'
            ])
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Get draft posts
     */
    public function getDraftPosts(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('status', 'draft')
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('created_at');

        return $this->paginate($perPage);
    }

    /**
     * Get scheduled posts
     */
    public function getScheduledPosts(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('status', 'scheduled')
            ->where('scheduled_for', '>', now())
            ->with(['user', 'categories', 'tags'])
            ->orderBy('scheduled_for');

        return $this->paginate($perPage);
    }

    /**
     * Get featured posts
     */
    public function getFeaturedPosts(int $limit = 3): Collection
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'read_time', 'published_at')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('is_featured', true)
            ->with(['user:id,name', 'categories:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Find post by slug
     */
    public function findBySlug(string $slug): ?NewsPost
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['user', 'categories', 'tags', 'seoDetail', 'approvedComments.user'])
            ->first();
    }

    /**
     * Get posts by category
     */
    public function getByCategory(string $categorySlug, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->whereHas('categories', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('published_at');

        return $this->paginate($perPage);
    }

    /**
     * Get posts by tag
     */
    public function getByTag(string $tagSlug, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->whereHas('tags', function ($query) use ($tagSlug) {
                $query->where('slug', $tagSlug);
            })
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('published_at');

        return $this->paginate($perPage);
    }

    /**
     * Get posts by author
     */
    public function getByAuthor(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('user_id', $userId)
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('created_at');

        return $this->paginate($perPage);
    }

    /**
     * Search posts
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('sub_title', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%")
                    ->orWhere('excerpt', 'like', "%{$keyword}%");
            })
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('published_at');

        return $this->paginate($perPage);
    }

    /**
     * Get latest posts
     */
    public function getLatestPosts(int $limit = 5): Collection
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'read_time', 'published_at')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user:id,name', 'categories:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular posts
     */
    public function getPopularPosts(int $limit = 5): Collection
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'read_time', 'views', 'published_at')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user:id,name', 'categories:id,name,slug,color'])
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection
    {
        $post = $this->findOrFail($postId, ['*'], ['categories']);
        
        if ($post->categories->isEmpty()) {
            return collect();
        }

        $categoryIds = $post->categories->pluck('id');

        return $this->model
            ->where('id', '!=', $postId)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('news_category_id', $categoryIds);
            })
            ->with(['user', 'categories'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Increment views
     */
    public function incrementViews(string $id): bool
    {
        $post = $this->findOrFail($id);
        $post->incrementViews();
        return true;
    }

    /**
     * Publish post
     */
    public function publish(string $id): bool
    {
        $post = $this->findOrFail($id);
        return $post->publish();
    }

    /**
     * Unpublish post
     */
    public function unpublish(string $id): bool
    {
        $post = $this->findOrFail($id);
        return $post->unpublish();
    }

    /**
     * Schedule post
     */
    public function schedule(string $id, \DateTime $dateTime): bool
    {
        $post = $this->findOrFail($id);
        return $post->schedule($dateTime);
    }

    /**
     * Feature post
     */
    public function feature(string $id): bool
    {
        $post = $this->findOrFail($id);
        return $post->feature();
    }

    /**
     * Unfeature post
     */
    public function unfeature(string $id): bool
    {
        $post = $this->findOrFail($id);
        return $post->unfeature();
    }
    /**
     * Get post statistics
     */
    public function getStatistics(): array
    {
        $query = $this->model->newQuery();

        // If not admin/authorized, only show own stats
        $user = auth()->user();
        if ($user && !$user->hasPermission('pengguna.view')) {
            $query->where('user_id', $user->id);
        }

        $totalViews = (int)$query->sum('views');

        return [
            'total' => $query->count(),
            'published' => (clone $query)->where('status', 'published')->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'views' => $totalViews,
            'comments_count' => \Illuminate\Support\Facades\DB::table('news_comments')
                ->join('news_posts', 'news_comments.news_post_id', '=', 'news_posts.id')
                ->when($user && !$user->hasPermission('pengguna.view'), function($q) use ($user) {
                    $q->where('news_posts.user_id', $user->id);
                })
                ->where('news_comments.approved', true)
                ->count(),
        ];
    }
}