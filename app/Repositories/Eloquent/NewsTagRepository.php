<?php

namespace App\Repositories\Eloquent;

use App\Models\News\NewsTag;
use App\Repositories\Contracts\NewsTagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class NewsTagRepository extends BaseRepository implements NewsTagRepositoryInterface
{
    public function __construct(NewsTag $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tags with posts count
     */
    public function getTagsWithPostsCount(): Collection
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
     * Find tag by slug
     */
    public function findBySlug(string $slug): ?NewsTag
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
     * Get tags with published posts
     */
    public function getTagsWithPublishedPosts(): Collection
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
     * Get popular tags
     */
    public function getPopularTags(int $limit = 10): Collection
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

    /**
     * Find or create tag
     */
    public function findOrCreateByName(string $name): NewsTag
    {
        $slug = Str::slug($name);
        
        $tag = $this->model->where('slug', $slug)->first();
        
        if ($tag) {
            return $tag;
        }

        return $this->create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}