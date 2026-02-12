<?php

namespace App\Services\News;

use App\Models\News\NewsSeoDetail;
use App\Repositories\Contracts\NewsPostRepositoryInterface;
use Illuminate\Support\Str;

class NewsSeoService
{
    public function __construct(
        private NewsPostRepositoryInterface $newsPostRepository
    ) {}

    /**
     * Create or update SEO details
     */
    public function createOrUpdateSeo(string $postId, array $data): NewsSeoDetail
    {
        // Verify post exists
        $post = $this->newsPostRepository->findOrFail($postId);

        // Get or create SEO detail
        $seoDetail = $post->seoDetail;

        if (!$seoDetail) {
            $seoDetail = new NewsSeoDetail();
            $seoDetail->news_post_id = $postId;
        }

        // Update fields
        $seoDetail->meta_title = $data['meta_title'] ?? $post->title;
        $seoDetail->meta_description = $data['meta_description'] ?? $post->excerpt ?? Str::limit(strip_tags($post->body), 160);
        $seoDetail->keywords = $data['keywords'] ?? [];

        $seoDetail->save();

        return $seoDetail;
    }

    /**
     * Generate meta description from content
     */
    public function generateMetaDescription(string $content, int $maxLength = 160): string
    {
        $stripped = strip_tags($content);
        $cleaned = preg_replace('/\s+/', ' ', $stripped);
        
        return Str::limit($cleaned, $maxLength);
    }

    /**
     * Extract keywords from content
     */
    public function extractKeywords(string $content, int $limit = 10): array
    {
        $stripped = strip_tags($content);
        $words = str_word_count(strtolower($stripped), 1);

        // Remove common words
        $commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were'];
        $words = array_diff($words, $commonWords);

        // Count word frequency
        $wordCount = array_count_values($words);
        arsort($wordCount);

        // Get top keywords
        $keywords = array_slice(array_keys($wordCount), 0, $limit);

        return $keywords;
    }

    /**
     * Get SEO score for post
     */
    public function getSeoScore(string $postId): array
    {
        $post = $this->newsPostRepository->findOrFail($postId);
        $seo = $post->seoDetail;

        $score = 0;
        $recommendations = [];

        // Check title length
        $titleLength = strlen($post->title);
        if ($titleLength >= 50 && $titleLength <= 60) {
            $score += 20;
        } else {
            $recommendations[] = 'Title should be between 50-60 characters';
        }

        // Check meta description
        if ($seo && $seo->meta_description) {
            $descLength = strlen($seo->meta_description);
            if ($descLength >= 150 && $descLength <= 160) {
                $score += 20;
            } else {
                $recommendations[] = 'Meta description should be between 150-160 characters';
            }
        } else {
            $recommendations[] = 'Add meta description';
        }

        // Check keywords
        if ($seo && !empty($seo->keywords)) {
            $score += 20;
        } else {
            $recommendations[] = 'Add SEO keywords';
        }

        // Check content length
        $wordCount = str_word_count(strip_tags($post->body));
        if ($wordCount >= 300) {
            $score += 20;
        } else {
            $recommendations[] = 'Content should be at least 300 words';
        }

        // Check if has featured image
        if ($post->cover_photo_path) {
            $score += 20;
        } else {
            $recommendations[] = 'Add featured image';
        }

        return [
            'score' => $score,
            'max_score' => 100,
            'percentage' => $score,
            'recommendations' => $recommendations,
        ];
    }
}