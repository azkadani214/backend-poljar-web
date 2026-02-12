<?php

namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogTagService;
use App\Http\Resources\V1\Blog\BlogTagResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function __construct(
        private BlogTagService $blogTagService
    ) {}

    /**
     * Get all blog tags
     * 
     * @group Blog
     */
    public function index(): JsonResponse
    {
        try {
            $tags = $this->blogTagService->getAllTags();

            return ResponseHelper::success(
                BlogTagResource::collection($tags),
                'Blog tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get tag by slug
     * 
     * @group Blog
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $tag = $this->blogTagService->getTagBySlug($slug);

            return ResponseHelper::success(
                new BlogTagResource($tag),
                'Blog tag retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Get tags with published posts
     * 
     * @group Blog
     */
    public function withPublishedPosts(): JsonResponse
    {
        try {
            $tags = $this->blogTagService->getTagsWithPublishedPosts();

            return ResponseHelper::success(
                BlogTagResource::collection($tags),
                'Blog tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get popular tags
     * 
     * @group Blog
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $tags = $this->blogTagService->getPopularTags($limit);

            return ResponseHelper::success(
                BlogTagResource::collection($tags),
                'Popular blog tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}