<?php

namespace App\Http\Controllers\Api\V1\Analytics;

use App\Http\Controllers\Controller;
use App\Models\News\NewsPost;
use App\Models\Blog\BlogPost;
use App\Models\User;
use App\Models\News\NewsletterSubscriber;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Get analytics overview for dashboard
     * 
     * @group Analytics
     * @authenticated
     */
    public function getOverview(): JsonResponse
    {
        try {
            $data = [
                'news_count' => NewsPost::count(),
                'blog_count' => BlogPost::count(),
                'users_count' => User::count(),
                'subscribers_count' => NewsletterSubscriber::where('subscribed', true)->count(),
                
                // Growth placeholders
                'news_growth' => '+0',
                'blog_growth' => '+0',
                'users_growth' => '+0',
                'subscribers_growth' => '+0',
            ];

            return ResponseHelper::success(
                $data,
                'Analytics overview retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
