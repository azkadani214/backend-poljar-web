<?php

namespace App\Repositories\Eloquent;

use App\Models\Newsletter\NewsletterCampaign;
use App\Repositories\Contracts\NewsletterCampaignRepositoryInterface;

class NewsletterCampaignRepository extends BaseRepository implements NewsletterCampaignRepositoryInterface
{
    public function __construct(NewsletterCampaign $model)
    {
        parent::__construct($model);
    }
}
