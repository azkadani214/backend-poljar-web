<?php

namespace App\Repositories\Eloquent;

use App\Models\Newsletter\NewsletterTemplate;
use App\Repositories\Contracts\NewsletterTemplateRepositoryInterface;

class NewsletterTemplateRepository extends BaseRepository implements NewsletterTemplateRepositoryInterface
{
    public function __construct(NewsletterTemplate $model)
    {
        parent::__construct($model);
    }
}
