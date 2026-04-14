<?php

namespace App\Services\Blog;

use App\Models\Blog\BlogCategory as Category;
use App\Repositories\Contracts\BlogCategoryRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class BlogCategoryService
{
    public function __construct(
        private BlogCategoryRepositoryInterface $blogCategoryRepository
    ) {}

    /**
     * Get all blog categories
     */
    public function getAllCategories(): Collection
    {
        return $this->blogCategoryRepository->getAllWithPostCount();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(string $id): Category
    {
        $category = $this->blogCategoryRepository->find($id);

        if (!$category) {
            throw new NotFoundException('Blog category not found');
        }

        return $category;
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug)
    {
        $category = $this->blogCategoryRepository->findBySlug($slug);

        if (!$category) {
            throw new NotFoundException('Blog category not found');
        }

        return $category;
    }

    /**
     * Get categories with published posts
     */
    public function getCategoriesWithPublishedPosts(): Collection
    {
        return $this->blogCategoryRepository->getCategoriesWithPublishedPosts();
    }

    /**
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 10): Collection
    {
        return $this->blogCategoryRepository->getPopularCategories($limit);
    }

    /**
     * Create category
     */
    public function createCategory(array $data): Category
    {
        // Check if category name exists
        $existing = $this->blogCategoryRepository->findBy('name', $data['name']);
        if ($existing) {
            throw new ValidationException('Category name already exists');
        }

        return $this->blogCategoryRepository->create($data);
    }

    /**
     * Update category
     */
    public function updateCategory(string $id, array $data): Category
    {
        $category = $this->getCategoryById($id);

        // Check if new name exists (excluding current category)
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $existing = $this->blogCategoryRepository->findBy('name', $data['name']);
            if ($existing) {
                throw new ValidationException('Category name already exists');
            }
        }

        $this->blogCategoryRepository->update($id, $data);

        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function deleteCategory(string $id): bool
    {
        $category = $this->getCategoryById($id);

        // Check if category has posts
        if ($category->posts()->count() > 0) {
            throw new ValidationException(
                'Cannot delete category with existing posts. Please remove or reassign posts first.'
            );
        }

        return $this->blogCategoryRepository->delete($id);
    }
}