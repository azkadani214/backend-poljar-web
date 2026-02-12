<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NewsCommentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get comments by post
     */
    public function getByPost(string $postId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get approved comments by post
     */
    public function getApprovedByPost(string $postId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get pending comments
     */
    public function getPendingComments(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get comments by user
     */
    public function getByUser(string $userId): Collection;

    /**
     * Approve comment
     */
    public function approve(string $id): bool;

    /**
     * Reject comment
     */
    public function reject(string $id): bool;

    /**
     * Bulk approve comments
     */
    public function bulkApprove(array $ids): bool;

    /**
     * Bulk reject comments
     */
    public function bulkReject(array $ids): bool;
}