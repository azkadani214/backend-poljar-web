<?php

namespace App\Services\News;

use App\Models\News\NewsTag;
use App\Repositories\Contracts\NewsTagRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class NewsTagService
{
    public function __construct(
        private NewsTagRepositoryInterface $newsTagRepository
    ) {}

    /**
     * Get all tags
     */
    public function getAllTags(): Collection
    {
        return $this->newsTagRepository->getTagsWithPostsCount();
    }

    /**
     * Get tags with published posts (for public)
     */
    public function getTagsWithPublishedPosts(): Collection
    {
        return $this->newsTagRepository->getTagsWithPublishedPosts();
    }

    /**
     * Get popular tags
     */
    public function getPopularTags(int $limit = 10): Collection
    {
        return $this->newsTagRepository->getPopularTags($limit);
    }

    /**
     * Get tag by ID
     */
    public function getTagById(string $id): NewsTag
    {
        $tag = $this->newsTagRepository->find($id, ['*'], ['posts']);

        if (!$tag) {
            throw new NotFoundException('News tag not found');
        }

        return $tag;
    }

    /**
     * Get tag by slug
     */
    public function getTagBySlug(string $slug): NewsTag
    {
        $tag = $this->newsTagRepository->findBySlug($slug);

        if (!$tag) {
            throw new NotFoundException('News tag not found');
        }

        return $tag;
    }

    /**
     * Create tag
     */
    public function createTag(array $data): NewsTag
    {
        // Check if tag name exists
        $existing = $this->newsTagRepository->findBy('name', $data['name']);
        if ($existing) {
            throw new ValidationException('Tag name already exists');
        }

        return $this->newsTagRepository->create($data);
    }

    /**
     * Find or create tags by names
     */
    public function findOrCreateTags(array $tagNames): Collection
    {
        $tags = collect();

        foreach ($tagNames as $tagName) {
            $tag = $this->newsTagRepository->findOrCreateByName($tagName);
            $tags->push($tag);
        }

        return $tags;
    }

    /**
     * Update tag
     */
    public function updateTag(string $id, array $data): NewsTag
    {
        $tag = $this->getTagById($id);

        // Check if new name exists (excluding current tag)
        if (isset($data['name']) && $data['name'] !== $tag->name) {
            $existing = $this->newsTagRepository->findBy('name', $data['name']);
            if ($existing) {
                throw new ValidationException('Tag name already exists');
            }
        }

        $this->newsTagRepository->update($id, $data);

        return $tag->fresh();
    }

    /**
     * Delete tag
     */
    public function deleteTag(string $id): bool
    {
        $this->getTagById($id); // Check if exists

        // Tags can be deleted even if they have posts (many-to-many relationship)
        return $this->newsTagRepository->delete($id);
    }
}