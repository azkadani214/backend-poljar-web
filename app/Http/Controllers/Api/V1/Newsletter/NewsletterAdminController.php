<?php

namespace App\Http\Controllers\Api\V1\Newsletter;

use App\Http\Controllers\Controller;
use App\Services\Newsletter\NewsletterService;
use App\Services\Newsletter\CampaignService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterAdminController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService,
        private CampaignService $campaignService
    ) {}

    // ========================================================================
    // SUBSCRIBERS
    // ========================================================================

    public function subscribers(): JsonResponse
    {
        try {
            return ResponseHelper::success($this->newsletterService->getAllSubscribers());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            return ResponseHelper::success($this->newsletterService->getStatistics());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    // ========================================================================
    // TOPICS
    // ========================================================================

    public function topics(): JsonResponse
    {
        return ResponseHelper::success($this->newsletterService->getTopics());
    }

    public function storeTopic(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:newsletter_topics,slug',
            'description' => 'nullable|string',
            'is_default' => 'boolean'
        ]);

        $topic = \App\Models\Newsletter\NewsletterTopic::create($data);
        return ResponseHelper::created($topic);
    }

    // ========================================================================
    // CAMPAIGNS
    // ========================================================================

    public function campaigns(): JsonResponse
    {
        return ResponseHelper::success($this->campaignService->getAllCampaigns());
    }

    public function storeCampaign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'template_id' => 'required|exists:newsletter_templates,id',
            'topic_id' => 'nullable|exists:newsletter_topics,id',
        ]);

        $campaign = $this->campaignService->createCampaign($data);
        return ResponseHelper::created($campaign);
    }

    public function sendCampaign(string $id): JsonResponse
    {
        try {
            $this->campaignService->sendNow($id);
            return ResponseHelper::success(null, 'Campaign sending started.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    // ========================================================================
    // TEMPLATES
    // ========================================================================

    public function templates(): JsonResponse
    {
        return ResponseHelper::success(\App\Models\Newsletter\NewsletterTemplate::all());
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'meta' => 'nullable|array'
        ]);

        $template = \App\Models\Newsletter\NewsletterTemplate::create($data);
        return ResponseHelper::created($template);
    }
}
