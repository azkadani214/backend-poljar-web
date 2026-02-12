<?php

namespace App\Http\Controllers\Api\V1\Blog;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogCategoryService;
use App\Http\Resources\V1\Blog\BlogCategoryResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct(
        private BlogCategoryService $blogCategoryService
    ) {}

    /**
     * Get all blog categories
     * 
     * @group Blog
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->blogCategoryService->getAllCategories();

            return ResponseHelper::success(
                BlogCategoryResource::collection($categories),
                'Blog categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get category by slug
     * 
     * @group Blog
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $category = $this->blogCategoryService->getCategoryBySlug($slug);

            return ResponseHelper::success(
                new BlogCategoryResource($category),
                'Blog category retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Get categories with published posts
     * 
     * @group Blog
     */
    public function withPublishedPosts(): JsonResponse
    {
        try {
            $categories = $this->blogCategoryService->getCategoriesWithPublishedPosts();

            return ResponseHelper::success(
                BlogCategoryResource::collection($categories),
                'Blog categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get popular categories
     * 
     * @group Blog
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $categories = $this->blogCategoryService->getPopularCategories($limit);

            return ResponseHelper::success(
                BlogCategoryResource::collection($categories),
                'Popular blog categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}