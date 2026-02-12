<?php

namespace App\Services\Newsletter;

use App\Models\Newsletter\NewsletterCampaign;
use App\Repositories\Contracts\NewsletterCampaignRepositoryInterface;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use App\Exceptions\Api\ValidationException;

class CampaignService
{
    public function __construct(
        private NewsletterCampaignRepositoryInterface $campaignRepository,
        private NewsletterRepositoryInterface $subscriberRepository
    ) {}

    public function createCampaign(array $data): NewsletterCampaign
    {
        /** @var NewsletterCampaign $campaign */
        $campaign = $this->campaignRepository->create($data);
        return $campaign;
    }

    public function updateCampaign(string $id, array $data): NewsletterCampaign
    {
        $this->campaignRepository->update($id, $data);
        /** @var NewsletterCampaign $campaign */
        $campaign = $this->campaignRepository->find($id);
        return $campaign;
    }

    public function deleteCampaign(string $id): bool
    {
        return $this->campaignRepository->delete($id);
    }

    public function getCampaign(string $id): ?NewsletterCampaign
    {
        /** @var NewsletterCampaign|null $campaign */
        $campaign = $this->campaignRepository->find($id);
        return $campaign;
    }

    public function getAllCampaigns(): iterable
    {
        return $this->campaignRepository->all();
    }

    /**
     * Schedule a campaign
     */
    public function schedule(string $id, string $scheduledAt): NewsletterCampaign
    {
        /** @var NewsletterCampaign $campaign */
        $campaign = $this->campaignRepository->find($id);
        
        if ($campaign->status === 'sent') {
            throw new ValidationException('Cannot schedule a campaign that has already been sent');
        }

        $this->campaignRepository->update($id, [
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        /** @var NewsletterCampaign $updatedCampaign */
        $updatedCampaign = $this->campaignRepository->find($id);
        return $updatedCampaign;
    }

    /**
     * Send campaign now
     */
    public function sendNow(string $id): bool
    {
        /** @var NewsletterCampaign $campaign */
        $campaign = $this->campaignRepository->find($id);

        if ($campaign->status === 'sent' || $campaign->status === 'sending') {
            throw new ValidationException('Campaign is already sent or being sent');
        }

        // Update status to sending
        $this->campaignRepository->update($id, ['status' => 'sending']);

        // Dispatch send job
        \App\Jobs\SendCampaignJob::dispatch($campaign);

        return true;
    }
}
