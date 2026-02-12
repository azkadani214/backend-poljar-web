<?php
namespace App\Services\News;

use App\Models\News\NewsComment;
use App\Repositories\Contracts\NewsCommentRepositoryInterface;
use App\Repositories\Contracts\NewsPostRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsCommentService
{
    public function __construct(
        private NewsCommentRepositoryInterface $newsCommentRepository,
        private NewsPostRepositoryInterface $newsPostRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get comments by post
     */
    public function getCommentsByPost(string $postId, int $perPage = 15): LengthAwarePaginator
    {
        // Verify post exists
        $this->newsPostRepository->findOrFail($postId);

        return $this->newsCommentRepository->getByPost($postId, $perPage);
    }

    /**
     * Get approved comments by post (for public)
     */
    public function getApprovedCommentsByPost(string $postId, int $perPage = 15): LengthAwarePaginator
    {
        // Verify post exists
        $this->newsPostRepository->findOrFail($postId);

        return $this->newsCommentRepository->getApprovedByPost($postId, $perPage);
    }

    /**
     * Get pending comments (for admin)
     */
    public function getPendingComments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->newsCommentRepository->getPendingComments($perPage);
    }

    /**
     * Get comments by user
     */
    public function getCommentsByUser(string $userId): Collection
    {
        // Verify user exists
        $this->userRepository->findOrFail($userId);

        return $this->newsCommentRepository->getByUser($userId);
    }

    /**
     * Get comment by ID
     */
    public function getCommentById(string $id): NewsComment
    {
        $comment = $this->newsCommentRepository->find($id, ['*'], ['user', 'post']);

        if (!$comment) {
            throw new NotFoundException('Comment not found');
        }

        return $comment;
    }

    /**
     * Create comment
     */
    public function createComment(array $data): NewsComment
    {
        // Verify post exists and is published
        $post = $this->newsPostRepository->findOrFail($data['news_post_id']);
        
        if (!$post->isPublished()) {
            throw new ValidationException('Cannot comment on unpublished post');
        }

        // Verify user exists if provided
        if (isset($data['user_id'])) {
            $this->userRepository->findOrFail($data['user_id']);
        }

        // Set default approval status (false - needs approval)
        $data['approved'] = $data['approved'] ?? false;

        return $this->newsCommentRepository->create($data);
    }

    /**
     * Update comment
     */
    public function updateComment(string $id, array $data): NewsComment
    {
        $comment = $this->getCommentById($id);

        // Only allow updating comment text
        $allowedFields = ['comment'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            throw new ValidationException('No valid fields to update');
        }

        $this->newsCommentRepository->update($id, $updateData);

        return $comment->fresh();
    }

    /**
     * Delete comment
     */
    public function deleteComment(string $id): bool
    {
        $this->getCommentById($id); // Check if exists

        return $this->newsCommentRepository->delete($id);
    }

    /**
     * Approve comment
     */
    public function approveComment(string $id): NewsComment
    {
        $comment = $this->getCommentById($id);

        if ($comment->isApproved()) {
            throw new ValidationException('Comment is already approved');
        }

        $this->newsCommentRepository->approve($id);

        return $comment->fresh();
    }

    /**
     * Reject comment
     */
    public function rejectComment(string $id): NewsComment
    {
        $comment = $this->getCommentById($id);

        if (!$comment->isApproved()) {
            throw new ValidationException('Comment is already rejected');
        }

        $this->newsCommentRepository->reject($id);

        return $comment->fresh();
    }

    /**
     * Bulk approve comments
     */
    public function bulkApprove(array $commentIds): int
    {
        $result = $this->newsCommentRepository->bulkApprove($commentIds);

        return $result ? count($commentIds) : 0;
    }

    /**
     * Bulk reject comments
     */
    public function bulkReject(array $commentIds): int
    {
        $result = $this->newsCommentRepository->bulkReject($commentIds);

        return $result ? count($commentIds) : 0;
    }

    /**
     * Get all comments with filters (Admin)
     */
    public function getAllComments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->newsCommentRepository->paginate($perPage, ['*'], ['user', 'post'], $filters);
    }
}
