<?php


namespace App\Http\Controllers\Api\V1\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsPostRequest;
use App\Http\Requests\News\UpdateNewsPostRequest;
use App\Services\News\NewsPostService;
use App\Http\Resources\V1\News\NewsPostResource;
use App\Http\Resources\V1\News\NewsPostCollection;
use App\Http\Resources\V1\News\NewsPostDetailResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsPostController extends Controller
{
    public function __construct(
        private NewsPostService $newsPostService
    ) {}

    /**
     * Get all posts (Admin)
     * 
     * @group News Management
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['status', 'category', 'tag', 'author', 'search']);

            $posts = $this->newsPostService->getAllPosts($filters, $perPage);

            return ResponseHelper::paginated(
                $posts,
                NewsPostResource::class,
                'News posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get published posts (Public)
     * 
     * @group Public News
     */
    public function publicIndex(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->newsPostService->getPublishedPosts($perPage);

            return ResponseHelper::paginated(
                $posts,
                NewsPostResource::class,
                'News posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create news post
     * 
     * @group News Management
     * @authenticated
     */
    public function store(StoreNewsPostRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $post = $this->newsPostService->createPost($data);

            return ResponseHelper::created(
                new NewsPostDetailResource($post),
                'News post created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get post by slug or ID
     * 
     * @group Public News
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        try {
            // Check if it's a UUID
            if (\Illuminate\Support\Str::isUuid($idOrSlug)) {
                $post = $this->newsPostService->getPostById($idOrSlug);
            } else {
                $post = $this->newsPostService->getPostBySlug($idOrSlug);
            }

            // If not published and not admin, throw 404
            $user = $request->user('sanctum') ?? auth('sanctum')->user();
            if ($post->status !== 'published' && (!$user || !$user->hasPermission('berita.view'))) {
                return ResponseHelper::notFound('News post not found');
            }

            return ResponseHelper::success(
                new NewsPostDetailResource($post),
                'News post retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update news post
     * 
     * @group News Management
     * @authenticated
     */
    public function update(UpdateNewsPostRequest $request, string $id): JsonResponse
    {
        try {
            $post = $this->newsPostService->updatePost($id, $request->validated());

            return ResponseHelper::updated(
                new NewsPostDetailResource($post),
                'News post updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete news post
     * 
     * @group News Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->newsPostService->deletePost($id);

            return ResponseHelper::deleted('News post deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Publish post
     * 
     * @group News Management
     * @authenticated
     */
    public function publish(string $id): JsonResponse
    {
        try {
            $post = $this->newsPostService->publishPost($id);

            return ResponseHelper::success(
                new NewsPostResource($post),
                'News post published successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Unpublish post
     * 
     * @group News Management
     * @authenticated
     */
    public function unpublish(string $id): JsonResponse
    {
        try {
            $post = $this->newsPostService->unpublishPost($id);

            return ResponseHelper::success(
                new NewsPostResource($post),
                'News post unpublished successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Schedule post
     * 
     * @group News Management
     * @authenticated
     */
    public function schedule(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'scheduled_for' => 'required|date|after:now'
        ]);

        try {
            $post = $this->newsPostService->schedulePost(
                $id,
                new \DateTime($request->scheduled_for)
            );

            return ResponseHelper::success(
                new NewsPostResource($post),
                'News post scheduled successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Feature post
     * 
     * @group News Management
     * @authenticated
     */
    public function feature(string $id): JsonResponse
    {
        try {
            $post = $this->newsPostService->featurePost($id);

            return ResponseHelper::success(
                new NewsPostResource($post),
                'News post featured successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Unfeature post
     * 
     * @group News Management
     * @authenticated
     */
    public function unfeature(string $id): JsonResponse
    {
        try {
            $post = $this->newsPostService->unfeaturePost($id);

            return ResponseHelper::success(
                new NewsPostResource($post),
                'News post unfeatured successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get featured posts
     * 
     * @group Public News
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 3);
            $posts = $this->newsPostService->getFeaturedPosts($limit);

            return ResponseHelper::success(
                NewsPostResource::collection($posts),
                'Featured posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get latest posts
     * 
     * @group Public News
     */
    public function latest(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 5);
            $posts = $this->newsPostService->getLatestPosts($limit);

            return ResponseHelper::success(
                NewsPostResource::collection($posts),
                'Latest posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get popular posts
     * 
     * @group Public News
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 5);
            $posts = $this->newsPostService->getPopularPosts($limit);

            return ResponseHelper::success(
                NewsPostResource::collection($posts),
                'Popular posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get related posts
     * 
     * @group Public News
     */
    public function related(Request $request, string $postId): JsonResponse
    {
        try {
            $limit = $request->input('limit', 3);
            $posts = $this->newsPostService->getRelatedPosts($postId, $limit);

            return ResponseHelper::success(
                NewsPostResource::collection($posts),
                'Related posts retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Search posts
     * 
     * @group Public News
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2'
        ]);

        try {
            $perPage = $request->input('per_page', 15);
            $posts = $this->newsPostService->searchPosts($request->keyword, $perPage);

            return ResponseHelper::paginated(
                $posts,
                NewsPostResource::class,
                'Search results retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get post statistics
     * 
     * @group News Management
     * @authenticated
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->newsPostService->getPostStatistics();

            return ResponseHelper::success(
                $stats,
                'Post statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}