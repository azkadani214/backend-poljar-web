<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\News\NewsPost;
use App\Models\Blog\BlogPost;
use App\Models\Membership;
use App\Observers\UserObserver;
use App\Observers\NewsPostObserver;
use App\Observers\BlogPostObserver;
use App\Observers\MembershipObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        NewsPost::observe(NewsPostObserver::class);
        BlogPost::observe(BlogPostObserver::class);
        Membership::observe(MembershipObserver::class);
    }
}