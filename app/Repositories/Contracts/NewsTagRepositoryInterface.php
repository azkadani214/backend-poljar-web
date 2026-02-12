<?php

namespace App\Repositories\Contracts;

use App\Models\News\NewsTag;
use Illuminate\Database\Eloquent\Collection;

interface NewsTagRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get tags with posts count
     */
    public function getTagsWithPostsCount(): Collection;

    /**
     * Find tag by slug
     */
    public function findBySlug(string $slug): ?NewsTag;

    /**
     * Get tags with published posts
     */
    public function getTagsWithPublishedPosts(): Collection;

    /**
     * Get popular tags
     */
    public function getPopularTags(int $limit = 10): Collection;

    /**
     * Find or create tag
     */
    public function findOrCreateByName(string $name): NewsTag;
}