<?php

namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\BlogComment;
use App\Models\Blog\BlogPost;
use App\Helpers\ResponseHelper;
use App\Http\Resources\V1\Blog\BlogCommentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogCommentController extends Controller
{
    /**
     * Get approved comments for a post (Public)
     */
    public function publicIndex(string $postSlug): JsonResponse
    {
        try {
            $post = BlogPost::where('slug', $postSlug)->first();
            if (!$post) {
                return ResponseHelper::error('Post not found', 404);
            }
            $comments = $post->approvedComments()->orderByDesc('created_at')->get();

            return ResponseHelper::success(
                BlogCommentResource::collection($comments),
                'Blog comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Store a new comment (Public)
     */
    public function store(Request $request, string $postSlug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        try {
            $post = BlogPost::where('slug', $postSlug)->first();
            if (!$post) {
                return ResponseHelper::error('Post not found', 404);
            }

            $comment = new BlogComment();
            $comment->blog_post_id = $post->id;
            $comment->comment = $request->comment;
            
            if ($request->user()) {
                $comment->user_id = $request->user()->id;
            } else {
                $comment->name = $request->name;
                $comment->email = $request->email;
            }

            // By default, comments are pending approval
            $comment->approved = false; 
            $comment->save();

            return ResponseHelper::created(
                new BlogCommentResource($comment), // Use BlogCommentResource
                'Comment submitted successfully and awaiting approval'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get all comments (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $status = $request->input('status', 'all');

            $query = BlogComment::with(['post', 'user'])->orderByDesc('created_at');

            if ($status === 'pending') {
                $query->where('approved', false);
            } elseif ($status === 'approved') {
                $query->where('approved', true);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            $comments = $query->paginate($perPage);

            return ResponseHelper::paginated(
                $comments,
                BlogCommentResource::class,
                'All blog comments retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Approve a comment (Admin)
     */
    public function approve(string $id): JsonResponse
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->approve();

            return ResponseHelper::success($comment, 'Comment approved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Reject a comment (Admin)
     */
    public function reject(string $id): JsonResponse
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->reject();

            return ResponseHelper::success($comment, 'Comment rejected successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Delete a comment (Admin)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $comment = BlogComment::findOrFail($id);
            $comment->delete();

            return ResponseHelper::deleted('Comment deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
