<?php

namespace App\Services\Blog;

use App\Models\Blog\BlogCategory as Category;
use Illuminate\Database\Eloquent\Collection;


class BlogCategoryService
{
    /**
     * Get all blog categories
     */
    public function getAllCategories(): Collection
    {
        return Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published')
                ->where('published_at', '<=', now());
        }])
        ->orderBy('name')
        ->get();
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->first();

        if (!$category) {
            throw new \App\Exceptions\Api\NotFoundException('Blog category not found');
        }

        return $category;
    }

    /**
     * Get categories with published posts
     */
    public function getCategoriesWithPublishedPosts(): Collection
    {
        return Category::withCount(['posts' => function ($query) {
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
        return Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published')
                ->where('published_at', '<=', now());
        }])
        ->having('posts_count', '>', 0)
        ->orderByDesc('posts_count')
        ->limit($limit)
        ->get();
    }
}