<?php
namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogPostService;
use App\Http\Resources\V1\Blog\BlogPostResource;
use App\Http\Resources\V1\Blog\BlogPostDetailResource;
use App\Http\Requests\Blog\StoreBlogPostRequest;
use App\Http\Requests\Blog\UpdateBlogPostRequest;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function __construct(
        private BlogPostService $blogPostService
    ) {}

    /**
     * Get all blog posts (Admin)
     * 
     * @group Blog Management
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['status', 'category_id', 'search']);

            $posts = $this->blogPostService->getAllPosts($filters, $perPage);

            return ResponseHelper::paginated(
                $posts,
                BlogPostResource::class,
                'Blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create new blog post
     * 
     * @group Blog Management
     * @authenticated
     */
    public function store(StoreBlogPostRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $post = $this->blogPostService->createPost($data);

            return ResponseHelper::created(
                new BlogPostDetailResource($post),
                'Blog post created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Update blog post
     * 
     * @group Blog Management
     * @authenticated
     */
    public function update(UpdateBlogPostRequest $request, string $id): JsonResponse
    {
        try {
            $post = $this->blogPostService->updatePost($id, $request->validated());

            return ResponseHelper::updated(
                new BlogPostDetailResource($post),
                'Blog post updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Delete blog post
     * 
     * @group Blog Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->blogPostService->deletePost($id);
            return ResponseHelper::deleted('Blog post deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk delete blog posts
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array']);
        try {
            $this->blogPostService->bulkDelete($request->ids);
            return ResponseHelper::success(null, 'Blog posts deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk publish blog posts
     */
    public function bulkPublish(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array']);
        try {
            $this->blogPostService->bulkUpdateStatus($request->ids, 'published');
            return ResponseHelper::success(null, 'Blog posts published successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get published blog posts
     * 
     * @group Blog
     */
    public function publicIndex(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->blogPostService->getPublishedPosts($perPage);

            return ResponseHelper::paginated(
                $posts,
                BlogPostResource::class,
                'Blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get blog post by slug
     * 
     * @group Blog
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        try {
            if (Str::isUuid($idOrSlug)) {
                $post = $this->blogPostService->getPostById($idOrSlug);
            } else {
                $post = $this->blogPostService->getPostBySlug($idOrSlug);
            }

            if (!$post) {
                return ResponseHelper::notFound('Blog post not found');
            }

            $user = $request->user('sanctum') ?? auth('sanctum')->user();
            if ($post->status !== 'published' && (!$user || !$user->hasPermission('blog.view'))) {
                return ResponseHelper::notFound('Blog post not found');
            }

            return ResponseHelper::success(
                new BlogPostDetailResource($post),
                'Blog post retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    public function incrementViews(string $slug): JsonResponse
    {
        try {
            $post = $this->blogPostService->getPostBySlug($slug);
            if (!$post) {
                return ResponseHelper::notFound('Blog post not found');
            }

            $post->increment('views');

            return ResponseHelper::success(null, 'View count incremented');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get blog post statistics
     * 
     * @group Blog Management
     * @authenticated
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->blogPostService->getPostStatistics();

            return ResponseHelper::success(
                $stats,
                'Blog post statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get latest blog posts
     * 
     * @group Blog
     */
    public function latest(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 3);
            $posts = $this->blogPostService->getLatestPosts($limit);

            return ResponseHelper::success(
                BlogPostResource::collection($posts),
                'Latest blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Search blog posts
     * 
     * @group Blog
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2'
        ]);

        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->blogPostService->searchPosts($request->keyword, $perPage);

            return ResponseHelper::paginated(
                $posts,
                BlogPostResource::class,
                'Search results retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get posts by category
     * 
     * @group Blog
     */
    public function byCategory(Request $request, string $categorySlug): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->blogPostService->getPostsByCategory($categorySlug, $perPage);

            return ResponseHelper::paginated(
                $posts,
                BlogPostResource::class,
                'Blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get posts by tag
     * 
     * @group Blog
     */
    public function byTag(Request $request, string $tagSlug): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->blogPostService->getPostsByTag($tagSlug, $perPage);

            return ResponseHelper::paginated(
                $posts,
                BlogPostResource::class,
                'Blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
    /**
     * Get related blog posts
     * 
     * @group Blog
     */
    public function related(string $idOrSlug, Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 3);
            
            // First find the post to get its ID if slug is provided
            if (\Illuminate\Support\Str::isUuid($idOrSlug)) {
                $postId = $idOrSlug;
            } else {
                $post = $this->blogPostService->getPostBySlug($idOrSlug);
                if (!$post) {
                    return ResponseHelper::notFound('Blog post not found');
                }
                $postId = $post->id;
            }

            $posts = $this->blogPostService->getRelatedPosts($postId, $limit);

            return ResponseHelper::success(
                BlogPostResource::collection($posts),
                'Related blog posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}