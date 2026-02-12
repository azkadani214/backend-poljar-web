<?php

namespace App\Http\Controllers\Api\V1\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Services\Newsletter\NewsletterService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {}

    /**
     * Subscribe to newsletter
     * 
     * @group Newsletter
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        try {
            $subscriber = $this->newsletterService->subscribe($request->email);

            return ResponseHelper::success(
                ['email' => $subscriber->email],
                'Successfully subscribed to newsletter. Please check your email for verification.'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Unsubscribe from newsletter
     * 
     * @group Newsletter
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:newsletter_subscribers,email'
        ]);

        try {
            $this->newsletterService->unsubscribe($request->email);

            return ResponseHelper::success(
                null,
                'Successfully unsubscribed from newsletter'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Verify newsletter subscription
     * 
     * @group Newsletter
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        try {
            $this->newsletterService->verifySubscriber($request->token);

            return ResponseHelper::success(
                null,
                'Email verified successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get all subscribers (Admin)
     * 
     * @group Newsletter
     * @authenticated
     */
    public function subscribers(): JsonResponse
    {
        try {
            $subscribers = $this->newsletterService->getAllSubscribers();

            return ResponseHelper::success(
                $subscribers,
                'Subscribers retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get newsletter statistics
     * 
     * @group Newsletter
     * @authenticated
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->newsletterService->getStatistics();

            return ResponseHelper::success(
                $stats,
                'Newsletter statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Send newsletter (Admin)
     * 
     * @group Newsletter
     * @authenticated
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        try {
            $result = $this->newsletterService->sendNewsletter($request->only(['subject', 'content']));

            return ResponseHelper::success(
                $result,
                'Newsletter sent successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
