<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Blog\BlogSeoDetail;
use App\Models\Blog\BlogTag;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    /**
     * Create new blog post
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload
            if (isset($data['cover_photo']) && $data['cover_photo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['cover_photo_path'] = $data['cover_photo']->store('blog', 'public');
            }

            // Excerpt handling
            if (!isset($data['excerpt']) || empty($data['excerpt'])) {
                $data['excerpt'] = substr(strip_tags($data['body']), 0, 200);
            }

            // Status handling
            if ($data['status'] === 'published' && !isset($data['published_at'])) {
                $data['published_at'] = now();
            }

            $post = $this->model->create($data);

            // Sync categories
            if (isset($data['category_ids'])) {
                $post->categories()->sync($data['category_ids']);
            }

            // Sync tags
            if (isset($data['tag_ids'])) {
                $processedTagIds = [];
                foreach ($data['tag_ids'] as $tag) {
                    // Check if it's a UUID (existing tag) or a name (new tag)
                    if (Str::isUuid($tag)) {
                        $processedTagIds[] = $tag;
                    } else {
                        $newTag = BlogTag::firstOrCreate(
                            ['name' => $tag],
                            ['slug' => Str::slug($tag)]
                        );
                        $processedTagIds[] = $newTag->id;
                    }
                }
                $post->tags()->sync($processedTagIds);
            }

            // Sync SEO
            if (isset($data['seo'])) {
                $post->seoDetail()->create([
                    'meta_title' => $data['seo']['meta_title'] ?? null,
                    'meta_description' => $data['seo']['meta_description'] ?? null,
                    'keywords' => $data['seo']['keywords'] ?? null,
                ]);
            }

            return $post->load(['categories', 'tags', 'seoDetail']);
        });
    }

    /**
     * Update blog post
     */
    public function update(string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $post = $this->model->where('id', $id)->orWhere('slug', $id)->firstOrFail();

            // Handle image upload
            if (isset($data['cover_photo']) && $data['cover_photo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($post->cover_photo_path) {
                    Storage::disk('public')->delete($post->cover_photo_path);
                }
                $data['cover_photo_path'] = $data['cover_photo']->store('blog', 'public');
            }

            // Relationship data to sync after update
            $categoryIds = $data['category_ids'] ?? null;
            $tagIds = $data['tag_ids'] ?? null;
            $seoData = $data['seo'] ?? null;

            // Remove relationship data from $data to avoid mass-assignment errors
            unset($data['category_ids'], $data['tag_ids'], $data['seo'], $data['cover_photo']);

            // Excerpt handling
            if (isset($data['body']) && (!isset($data['excerpt']) || empty($data['excerpt']))) {
                $data['excerpt'] = Str::limit(strip_tags($data['body']), 200);
            }

            // Status & Dates handling
            if (isset($data['status'])) {
                if ($data['status'] === 'published' && !$post->published_at) {
                    $data['published_at'] = $data['published_at'] ?? now();
                } elseif ($data['status'] === 'scheduled' && isset($data['published_at'])) {
                    $data['scheduled_for'] = $data['published_at'];
                }
            }

            $post->update($data);

            // Sync categories
            if ($categoryIds !== null) {
                $post->categories()->sync($categoryIds);
            }

            // Sync tags
            if ($tagIds !== null) {
                $processedTagIds = [];
                foreach ($tagIds as $tag) {
                    if (Str::isUuid($tag)) {
                        $processedTagIds[] = $tag;
                    } else {
                        $newTag = \App\Models\Blog\BlogTag::firstOrCreate(
                            ['name' => $tag],
                            ['slug' => Str::slug($tag)]
                        );
                        $processedTagIds[] = $newTag->id;
                    }
                }
                $post->tags()->sync($processedTagIds);
            }

            // Sync SEO
            if ($seoData !== null) {
                $post->seoDetail()->updateOrCreate(
                    ['blog_post_id' => $post->id],
                    [
                        'meta_title' => $seoData['meta_title'] ?? null,
                        'meta_description' => $seoData['meta_description'] ?? null,
                        'keywords' => $seoData['keywords'] ?? null,
                    ]
                );
            }

            return $post->fresh(['categories', 'tags', 'seoDetail']);
        });
    }
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }


    /**
     * Get published blog posts
     */
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'published_at')
            ->where('status', 'published')
            ->whereNotNull('published_at')
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
     * Find blog post by slug
     */
    public function findBySlug(string $slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user', 'categories', 'tags', 'seoDetail'])
            ->first();
    }

    /**
     * Get latest blog posts
     */
    public function getLatestPosts(int $limit = 3): Collection
    {
        return $this->model->newQuery()
            ->select('id', 'user_id', 'title', 'slug', 'excerpt', 'cover_photo_path', 'published_at')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['user:id,name', 'categories:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search blog posts
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('sub_title', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%");
            })
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['user', 'categories', 'tags'])
            ->orderByDesc('published_at');

        return $this->paginate($perPage);
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
     * Get all posts with filters (Admin)
     */
    public function getAllPosts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['user', 'categories', 'tags']);

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id']) && $filters['category_id'] !== 'all') {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('blog_categories.id', $filters['category_id']);
            });
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('body', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Get blog statistics
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
            'comments_count' => DB::table('blog_comments')
                ->join('blog_posts', 'blog_comments.blog_post_id', '=', 'blog_posts.id')
                ->when($user && !$user->hasPermission('pengguna.view'), function($q) use ($user) {
                    $q->where('blog_posts.user_id', $user->id);
                })
                ->where('blog_comments.approved', true)
                ->count(),
        ];
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, string $status): bool
    {
        return $this->model->whereIn('id', $ids)->update(['status' => $status]);
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(array $ids): bool
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Get related blog posts
     */
    public function getRelatedPosts(string $postId, int $limit = 3): Collection
    {
        $post = $this->model->where('id', $postId)->orWhere('slug', $postId)->with('categories')->firstOrFail();
        
        if ($post->categories->isEmpty()) {
            return collect();
        }

        $categoryIds = $post->categories->pluck('id');

        return $this->model->newQuery()
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            })
            ->with(['user:id,name', 'categories:id,name,slug,color'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}