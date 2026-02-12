<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // User Repository
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        // Division Repository
        $this->app->bind(
            \App\Repositories\Contracts\DivisionRepositoryInterface::class,
            \App\Repositories\Eloquent\DivisionRepository::class
        );

        // Position Repository
        $this->app->bind(
            \App\Repositories\Contracts\PositionRepositoryInterface::class,
            \App\Repositories\Eloquent\PositionRepository::class
        );

        // Membership Repository
        $this->app->bind(
            \App\Repositories\Contracts\MembershipRepositoryInterface::class,
            \App\Repositories\Eloquent\MembershipRepository::class
        );

        // News Post Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsPostRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsPostRepository::class
        );

        // News Category Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsCategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsCategoryRepository::class
        );

        // News Tag Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsTagRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsTagRepository::class
        );

        // News Comment Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsCommentRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsCommentRepository::class
        );

        // Blog Post Repository
        $this->app->bind(
            \App\Repositories\Contracts\BlogPostRepositoryInterface::class,
            \App\Repositories\Eloquent\BlogPostRepository::class
        );

        // Blog Category Repository
        $this->app->bind(
            \App\Repositories\Contracts\BlogCategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\BlogCategoryRepository::class
        );

        // Blog Tag Repository
        $this->app->bind(
            \App\Repositories\Contracts\BlogTagRepositoryInterface::class,
            \App\Repositories\Eloquent\BlogTagRepository::class
        );

        // Newsletter Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsletterRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsletterRepository::class
        );

        // Newsletter Topic Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsletterTopicRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsletterTopicRepository::class
        );

        // Newsletter Template Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsletterTemplateRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsletterTemplateRepository::class
        );

        // Newsletter Campaign Repository
        $this->app->bind(
            \App\Repositories\Contracts\NewsletterCampaignRepositoryInterface::class,
            \App\Repositories\Eloquent\NewsletterCampaignRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}