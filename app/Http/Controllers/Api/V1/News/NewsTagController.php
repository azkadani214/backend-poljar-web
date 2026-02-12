<?php

namespace App\Http\Controllers\Api\V1\News;

use App\Http\Controllers\Controller;
use App\Services\News\NewsTagService;
use App\Http\Resources\V1\News\NewsTagResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsTagController extends Controller
{
    public function __construct(
        private NewsTagService $newsTagService
    ) {}

    /**
     * Get all tags
     * 
     * @group News Management
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try {
            $tags = $this->newsTagService->getAllTags();

            return ResponseHelper::success(
                NewsTagResource::collection($tags),
                'News tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get tags with published posts (Public)
     * 
     * @group Public News
     */
    public function publicIndex(): JsonResponse
    {
        try {
            $tags = $this->newsTagService->getTagsWithPublishedPosts();

            return ResponseHelper::success(
                NewsTagResource::collection($tags),
                'News tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create tag
     * 
     * @group News Management
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:news_tags,name'
        ]);

        try {
            $tag = $this->newsTagService->createTag($request->only('name'));

            return ResponseHelper::created(
                new NewsTagResource($tag),
                'News tag created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get tag by ID
     * 
     * @group News Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $tag = $this->newsTagService->getTagById($id);

            return ResponseHelper::success(
                new NewsTagResource($tag),
                'News tag retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Get tag by slug (Public)
     * 
     * @group Public News
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $tag = $this->newsTagService->getTagBySlug($slug);

            return ResponseHelper::success(
                new NewsTagResource($tag),
                'News tag retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update tag
     * 
     * @group News Management
     * @authenticated
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:50'
        ]);

        try {
            $tag = $this->newsTagService->updateTag($id, $request->only('name'));

            return ResponseHelper::updated(
                new NewsTagResource($tag),
                'News tag updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete tag
     * 
     * @group News Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->newsTagService->deleteTag($id);

            return ResponseHelper::deleted('News tag deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get popular tags
     * 
     * @group Public News
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $tags = $this->newsTagService->getPopularTags($limit);

            return ResponseHelper::success(
                NewsTagResource::collection($tags),
                'Popular tags retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}