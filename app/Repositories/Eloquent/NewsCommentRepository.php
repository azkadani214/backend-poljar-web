<?php

namespace App\Repositories\Eloquent;

use App\Models\News\NewsComment;
use App\Repositories\Contracts\NewsCommentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsCommentRepository extends BaseRepository implements NewsCommentRepositoryInterface
{
    public function __construct(NewsComment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get comments by post
     */
    public function getByPost(string $postId, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('news_post_id', $postId)
            ->with('user')
            ->orderByDesc('created_at');

        return $this->paginate($perPage);
    }

    /**
     * Get approved comments by post
     */
    public function getApprovedByPost(string $postId, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('news_post_id', $postId)
            ->where('approved', true)
            ->with('user')
            ->orderByDesc('created_at');

        return $this->paginate($perPage);
    }

    /**
     * Get pending comments
     */
    public function getPendingComments(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('approved', false)
            ->with(['user', 'post'])
            ->orderByDesc('created_at');

        return $this->paginate($perPage);
    }

    /**
     * Get comments by user
     */
    public function getByUser(string $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with('post')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Approve comment
     */
    public function approve(string $id): bool
    {
        $comment = $this->findOrFail($id);
        return $comment->approve();
    }

    /**
     * Reject comment
     */
    public function reject(string $id): bool
    {
        $comment = $this->findOrFail($id);
        return $comment->reject();
    }

    /**
     * Bulk approve comments
     */
    public function bulkApprove(array $ids): bool
    {
        return $this->model
            ->whereIn('id', $ids)
            ->update([
                'approved' => true,
                'approved_at' => now(),
            ]);
    }

    /**
     * Bulk reject comments
     */
    public function bulkReject(array $ids): bool
    {
        return $this->model
            ->whereIn('id', $ids)
            ->update([
                'approved' => false,
                'approved_at' => null,
            ]);
    }
}