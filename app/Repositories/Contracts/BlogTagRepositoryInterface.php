<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogTagRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all tags with post count
     */
    public function getAllWithPostCount(): Collection;

    /**
     * Find tag by slug
     */
    public function findBySlug(string $slug);

    /**
     * Get tags with published posts
     */
    public function getTagsWithPublishedPosts(): Collection;

    /**
     * Get popular tags
     */
    public function getPopularTags(int $limit = 10): Collection;
}
