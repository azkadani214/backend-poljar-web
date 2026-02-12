<?php

namespace App\Http\Controllers\Api\V1\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreCommentRequest;
use App\Http\Requests\News\UpdateCommentRequest;
use App\Services\News\NewsCommentService;
use App\Http\Resources\V1\News\NewsCommentResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsCommentController extends Controller
{
    public function __construct(
        private NewsCommentService $newsCommentService
    ) {}

    /**
     * Get comments by post (Admin - all comments)
     * 
     * @group News Management
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            $filters = [];
            if ($request->has('post_id')) {
                $filters['news_post_id'] = $request->post_id;
            }
            if ($request->has('user_id')) {
                $filters['user_id'] = $request->user_id;
            }
            
            // Handle status filter: all, pending, approved
            if ($request->has('status') && $request->status !== 'all') {
                $filters['approved'] = $request->status === 'approved';
            } elseif ($request->has('approved')) {
                $filters['approved'] = $request->boolean('approved');
            }

            $comments = $this->newsCommentService->getAllComments($filters, $perPage);

            return ResponseHelper::paginated(
                $comments,
                NewsCommentResource::class,
                'Comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get approved comments by post (Public)
     * 
     * @group Public News
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $request->validate([
            'post_id' => 'required|string|exists:news_posts,id'
        ]);

        try {
            $perPage = $request->input('per_page', 15);
            $comments = $this->newsCommentService->getApprovedCommentsByPost($request->post_id, $perPage);

            return ResponseHelper::paginated(
                $comments,
                NewsCommentResource::class,
                'Comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get pending comments
     * 
     * @group News Management
     * @authenticated
     */
    public function pending(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $comments = $this->newsCommentService->getPendingComments($perPage);

            return ResponseHelper::paginated(
                $comments,
                NewsCommentResource::class,
                'Pending comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create comment
     * 
     * @group Public News
     * @authenticated
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->user()) {
                $data['user_id'] = $request->user()->id;
            }

            $comment = $this->newsCommentService->createComment($data);

            return ResponseHelper::created(
                new NewsCommentResource($comment),
                'Comment created and awaiting approval'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get comment by ID
     * 
     * @group News Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $comment = $this->newsCommentService->getCommentById($id);

            return ResponseHelper::success(
                new NewsCommentResource($comment),
                'Comment retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update comment
     * 
     * @group News Management
     * @authenticated
     */
    public function update(UpdateCommentRequest $request, string $id): JsonResponse
    {
        try {
            $comment = $this->newsCommentService->updateComment($id, $request->validated());

            return ResponseHelper::updated(
                new NewsCommentResource($comment),
                'Comment updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete comment
     * 
     * @group News Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->newsCommentService->deleteComment($id);

            return ResponseHelper::deleted('Comment deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Approve comment
     * 
     * @group News Management
     * @authenticated
     */
    public function approve(string $id): JsonResponse
    {
        try {
            $comment = $this->newsCommentService->approveComment($id);

            return ResponseHelper::success(
                new NewsCommentResource($comment),
                'Comment approved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Reject comment
     * 
     * @group News Management
     * @authenticated
     */
    public function reject(string $id): JsonResponse
    {
        try {
            $comment = $this->newsCommentService->rejectComment($id);

            return ResponseHelper::success(
                new NewsCommentResource($comment),
                'Comment rejected successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk approve comments
     * 
     * @group News Management
     * @authenticated
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'comment_ids' => 'required|array|min:1',
            'comment_ids.*' => 'exists:news_comments,id'
        ]);

        try {
            $count = $this->newsCommentService->bulkApprove($request->comment_ids);

            return ResponseHelper::success(
                ['approved_count' => $count],
                "{$count} comments approved successfully"
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk reject comments
     * 
     * @group News Management
     * @authenticated
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $request->validate([
            'comment_ids' => 'required|array|min:1',
            'comment_ids.*' => 'exists:news_comments,id'
        ]);

        try {
            $count = $this->newsCommentService->bulkReject($request->comment_ids);

            return ResponseHelper::success(
                ['rejected_count' => $count],
                "{$count} comments rejected successfully"
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
