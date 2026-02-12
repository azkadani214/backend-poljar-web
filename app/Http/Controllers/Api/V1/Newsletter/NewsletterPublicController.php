<?php

namespace App\Http\Controllers\Api\V1\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Services\Newsletter\NewsletterService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterPublicController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {}

    /**
     * Subscribe to newsletter
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        try {
            $subscriber = $this->newsletterService->subscribe(
                $request->email,
                $request->input('topic_ids', []),
                $request->input('locale', 'id')
            );

            return ResponseHelper::success(
                ['email' => $subscriber->email],
                'Successfully subscribed. Please check your email to verify your subscription.'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Verify newsletter subscription
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        try {
            $this->newsletterService->verifySubscriber($request->token);
            return ResponseHelper::success(null, 'Subscription verified successfully.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get subscriber preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        try {
            $subscriber = \App\Models\News\NewsletterSubscriber::where('token', $request->token)
                ->with('topics')
                ->firstOrFail();

            return ResponseHelper::success([
                'email' => $subscriber->email,
                'subscribed' => $subscriber->subscribed,
                'selected_topics' => $subscriber->topics->pluck('id'),
                'available_topics' => $this->newsletterService->getTopics()
            ]);
        } catch (\Exception $e) {
            return ResponseHelper::error('Invalid or expired preference link');
        }
    }

    /**
     * Update subscriber preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'topic_ids' => 'array'
        ]);

        try {
            $this->newsletterService->updatePreferences($request->token, $request->topic_ids);
            return ResponseHelper::success(null, 'Preferences updated successfully.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Unsubscribe
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $this->newsletterService->unsubscribe($request->email, $request->reason);
            return ResponseHelper::success(null, 'Successfully unsubscribed.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
