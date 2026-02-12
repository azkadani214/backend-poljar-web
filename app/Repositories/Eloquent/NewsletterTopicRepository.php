<?php

namespace App\Repositories\Eloquent;

use App\Models\Newsletter\NewsletterTopic;
use App\Repositories\Contracts\NewsletterTopicRepositoryInterface;

class NewsletterTopicRepository extends BaseRepository implements NewsletterTopicRepositoryInterface
{
    public function __construct(NewsletterTopic $model)
    {
        parent::__construct($model);
    }
}
