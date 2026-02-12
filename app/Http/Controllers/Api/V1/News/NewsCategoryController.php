<?php

namespace App\Http\Controllers\Api\V1\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsCategoryRequest;
use App\Http\Requests\News\UpdateNewsCategoryRequest;
use App\Services\News\NewsCategoryService;
use App\Http\Resources\V1\News\NewsCategoryResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    public function __construct(
        private NewsCategoryService $newsCategoryService
    ) {}

    /**
     * Get all categories
     * 
     * @group News Management
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->newsCategoryService->getAllCategories();

            return ResponseHelper::success(
                NewsCategoryResource::collection($categories),
                'News categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get categories with published posts (Public)
     * 
     * @group Public News
     */
    public function publicIndex(): JsonResponse
    {
        try {
            $categories = $this->newsCategoryService->getCategoriesWithPublishedPosts();

            return ResponseHelper::success(
                NewsCategoryResource::collection($categories),
                'News categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create category
     * 
     * @group News Management
     * @authenticated
     */
    public function store(StoreNewsCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->newsCategoryService->createCategory($request->validated());

            return ResponseHelper::created(
                new NewsCategoryResource($category),
                'News category created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get category by ID
     * 
     * @group News Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = $this->newsCategoryService->getCategoryById($id);

            return ResponseHelper::success(
                new NewsCategoryResource($category),
                'News category retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Get category by slug (Public)
     * 
     * @group Public News
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $category = $this->newsCategoryService->getCategoryBySlug($slug);

            return ResponseHelper::success(
                new NewsCategoryResource($category),
                'News category retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update category
     * 
     * @group News Management
     * @authenticated
     */
    public function update(UpdateNewsCategoryRequest $request, string $id): JsonResponse
    {
        try {
            $category = $this->newsCategoryService->updateCategory($id, $request->validated());

            return ResponseHelper::updated(
                new NewsCategoryResource($category),
                'News category updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete category
     * 
     * @group News Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->newsCategoryService->deleteCategory($id);

            return ResponseHelper::deleted('News category deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get popular categories
     * 
     * @group Public News
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $categories = $this->newsCategoryService->getPopularCategories($limit);

            return ResponseHelper::success(
                NewsCategoryResource::collection($categories),
                'Popular categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}