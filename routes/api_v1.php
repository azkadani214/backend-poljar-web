<?php
// ============================================================================
// FILE 117: routes/api_v1.php
// ============================================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;

// ...

use App\Http\Controllers\Api\V1\Blog\BlogTagController;
use App\Http\Controllers\Api\V1\Role\RoleController;
use App\Http\Controllers\Api\V1\News\NewsTagController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\Blog\BlogPostController;
use App\Http\Controllers\Api\V1\News\NewsPostController;
use App\Http\Controllers\Api\V1\News\NewsCommentController;
use App\Http\Controllers\Api\V1\Blog\BlogCategoryController;
use App\Http\Controllers\Api\V1\Blog\BlogCommentController;
use App\Http\Controllers\Api\V1\Division\DivisionController;
use App\Http\Controllers\Api\V1\News\NewsCategoryController;
use App\Http\Controllers\Api\V1\Position\PositionController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Membership\MembershipController;
use App\Http\Controllers\Api\V1\Analytics\AnalyticsController;
use App\Http\Controllers\Api\V1\Newsletter\NewsletterPublicController;
use App\Http\Controllers\Api\V1\Newsletter\NewsletterAdminController;
use App\Http\Controllers\Api\V1\Organization\OrganizationController;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|*/

// ============================================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================================

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
    Route::post('/validate-token', [PasswordResetController::class, 'validateToken']);
});

// Newsletter (Public)
Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe', [NewsletterPublicController::class, 'subscribe']);
    Route::get('/verify', [NewsletterPublicController::class, 'verify']);
    Route::get('/preferences', [NewsletterPublicController::class, 'getPreferences']);
    Route::post('/preferences', [NewsletterPublicController::class, 'updatePreferences']);
    Route::post('/unsubscribe', [NewsletterPublicController::class, 'unsubscribe']);
});

// Public News Routes
Route::prefix('public')->group(function () {
    // News Posts
    Route::get('/news/posts', [NewsPostController::class, 'publicIndex']);
    Route::get('/news/posts/featured', [NewsPostController::class, 'featured']);
    Route::get('/news/posts/latest', [NewsPostController::class, 'latest']);
    Route::get('/news/posts/popular', [NewsPostController::class, 'popular']);
    Route::get('/news/posts/{slug}', [NewsPostController::class, 'show']);
    Route::get('/news/posts/{id}/related', [NewsPostController::class, 'related']);
    Route::get('/news/search', [NewsPostController::class, 'search']);

    // News Categories
    Route::get('/news/categories', [NewsCategoryController::class, 'publicIndex']);
    Route::get('/news/categories/popular', [NewsCategoryController::class, 'popular']);
    Route::get('/news/categories/{slug}', [NewsCategoryController::class, 'showBySlug']);

    // News Tags
    Route::get('/news/tags', [NewsTagController::class, 'publicIndex']);
    Route::get('/news/tags/popular', [NewsTagController::class, 'popular']);
    Route::get('/news/tags/{slug}', [NewsTagController::class, 'showBySlug']);

    // News Comments (Approved only)
    Route::get('/news/comments', [NewsCommentController::class, 'publicIndex']);
    Route::post('/news/comments', [NewsCommentController::class, 'store']);

    // Public Blog Routes
    Route::get('/blog/posts', [BlogPostController::class, 'publicIndex']);
    Route::get('/blog/posts/latest', [BlogPostController::class, 'latest']);
    Route::get('/blog/posts/search', [BlogPostController::class, 'search']);
    Route::get('/blog/posts/{slug}', [BlogPostController::class, 'show']);
    Route::get('/blog/posts/{slug}/related', [BlogPostController::class, 'related']);
    Route::post('/blog/posts/{slug}/views', [BlogPostController::class, 'incrementViews']);
    Route::get('/blog/posts/category/{slug}', [BlogPostController::class, 'byCategory']);
    Route::get('/blog/posts/tag/{slug}', [BlogPostController::class, 'byTag']);
    
    // Blog Categories
    Route::get('/blog/categories', [BlogCategoryController::class, 'index']);
    Route::get('/blog/categories/with-posts', [BlogCategoryController::class, 'withPublishedPosts']);
    Route::get('/blog/categories/popular', [BlogCategoryController::class, 'popular']);
    Route::get('/blog/categories/{slug}', [BlogCategoryController::class, 'show']);
    
    // Blog Tags
    Route::get('/blog/tags', [BlogTagController::class, 'index']);
    Route::get('/blog/tags/with-posts', [BlogTagController::class, 'withPublishedPosts']);
    Route::get('/blog/tags/popular', [BlogTagController::class, 'popular']);
    Route::get('/blog/tags/{slug}', [BlogTagController::class, 'show']);

    // Blog Comments
    Route::get('/blog/posts/{slug}/comments', [BlogCommentController::class, 'publicIndex']);
    Route::post('/blog/posts/{slug}/comments', [BlogCommentController::class, 'store']);

    // Public Organization Routes
    Route::prefix('organization')->group(function () {
        Route::get('/core-team', [MembershipController::class, 'coreTeam']);
        Route::get('/staff', [MembershipController::class, 'staff']);
        Route::get('/divisions', [DivisionController::class, 'index']);
    });
});



// Public Organization Info
Route::prefix('organization')->group(function () {
    Route::get('/core-team', [MembershipController::class, 'coreTeam']);
    Route::get('/staff', [MembershipController::class, 'staff']);
});

// ============================================================================
// PROTECTED ROUTES (Authentication Required)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // ========================================================================
    // AUTHENTICATION
    // ========================================================================
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/change-password', [PasswordResetController::class, 'changePassword']);
    });

    // ========================================================================
    // ROLE MANAGEMENT
    // ========================================================================
    Route::prefix('roles')->middleware('permission:sistem.view')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/permissions', [RoleController::class, 'permissions']);
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:sistem.update');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:sistem.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:sistem.update');
        Route::post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->middleware('permission:sistem.update');
    });

    // ========================================================================
    // PROFILE MANAGEMENT
    // ========================================================================
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/change-password', [ProfileController::class, 'changePassword']);
        Route::post('/photo', [ProfileController::class, 'updatePhoto']);
        Route::delete('/photo', [ProfileController::class, 'deletePhoto']);
    });

    // ========================================================================
    // USER MANAGEMENT
    // ========================================================================
    Route::prefix('users')->middleware('permission:pengguna.view')->group(function () {
        Route::get('/activities', [UserController::class, 'allActivities'])->middleware('permission:log.view');
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store'])->middleware('permission:pengguna.create');
        Route::post('/import', [UserController::class, 'import'])->middleware('permission:pengguna.create');
        Route::get('/search', [UserController::class, 'search']);
        Route::get('/statistics', [UserController::class, 'statistics']);
        Route::post('/bulk-activate', [UserController::class, 'bulkActivate'])->middleware('permission:pengguna.update');
        Route::post('/bulk-deactivate', [UserController::class, 'bulkDeactivate'])->middleware('permission:pengguna.update');
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:pengguna.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:pengguna.delete');
        Route::get('/{id}/activities', [UserController::class, 'activities'])->middleware('permission:log.view');
        Route::post('/{id}/activate', [UserController::class, 'activate'])->middleware('permission:pengguna.update');
        Route::post('/{id}/deactivate', [UserController::class, 'deactivate'])->middleware('permission:pengguna.update');
    });

    // ========================================================================
    // ORGANIZATION MANAGEMENT
    // ========================================================================
    Route::prefix('organization')->group(function () {
        Route::get('/chart', [OrganizationController::class, 'chart']);
    });

    // ========================================================================
    // DIVISION MANAGEMENT
    // ========================================================================
    Route::prefix('divisions')->middleware('permission:organisasi.view')->group(function () {
        Route::get('/', [DivisionController::class, 'index']);
        Route::post('/', [DivisionController::class, 'store'])->middleware('permission:organisasi.create');
        Route::get('/with-active-members', [DivisionController::class, 'withActiveMembers']);
        Route::get('/{id}', [DivisionController::class, 'show']);
        Route::put('/{id}', [DivisionController::class, 'update'])->middleware('permission:organisasi.update');
        Route::delete('/{id}', [DivisionController::class, 'destroy'])->middleware('permission:organisasi.delete');
        Route::get('/{id}/statistics', [DivisionController::class, 'statistics']);
        Route::get('/{id}/members', [DivisionController::class, 'members']);
    });

    // ========================================================================
    // POSITION MANAGEMENT
    // ========================================================================
    Route::prefix('positions')->middleware('permission:organisasi.view')->group(function () {
        Route::get('/', [PositionController::class, 'index']);
        Route::post('/', [PositionController::class, 'store'])->middleware('permission:organisasi.create');
        Route::get('/core-team', [PositionController::class, 'coreTeam']);
        Route::get('/staff', [PositionController::class, 'staff']);
        Route::get('/by-level', [PositionController::class, 'byLevel']);
        Route::get('/division/{divisionId}', [PositionController::class, 'byDivision']);
        Route::get('/{id}', [PositionController::class, 'show']);
        Route::put('/{id}', [PositionController::class, 'update'])->middleware('permission:organisasi.update');
        Route::delete('/{id}', [PositionController::class, 'destroy'])->middleware('permission:organisasi.delete');
    });

    // ========================================================================
    // MEMBERSHIP MANAGEMENT
    // ========================================================================
    Route::prefix('memberships')->middleware('permission:organisasi.view')->group(function () {
        Route::get('/', [MembershipController::class, 'index']);
        Route::post('/', [MembershipController::class, 'store'])->middleware('permission:organisasi.create');
        Route::get('/by-period', [MembershipController::class, 'byPeriod']);
        Route::get('/user/{userId}', [MembershipController::class, 'byUser']);
        Route::get('/division/{divisionId}', [MembershipController::class, 'byDivision']);
        Route::get('/position/{positionId}', [MembershipController::class, 'byPosition']);
        Route::get('/{id}', [MembershipController::class, 'show']);
        Route::put('/{id}', [MembershipController::class, 'update'])->middleware('permission:organisasi.update');
        Route::delete('/{id}', [MembershipController::class, 'destroy'])->middleware('permission:organisasi.delete');
        Route::post('/{id}/activate', [MembershipController::class, 'activate'])->middleware('permission:organisasi.update');
        Route::post('/{id}/deactivate', [MembershipController::class, 'deactivate'])->middleware('permission:organisasi.update');
    });

    // ========================================================================
    // NEWS POST MANAGEMENT
    // ========================================================================
    Route::prefix('news/posts')->middleware('permission:berita.view')->group(function () {
        Route::get('/', [NewsPostController::class, 'index']);
        Route::post('/', [NewsPostController::class, 'store'])->middleware('permission:berita.create');
        Route::get('/statistics', [NewsPostController::class, 'statistics']);
        Route::get('/{id}', [NewsPostController::class, 'show']);
        Route::put('/{id}', [NewsPostController::class, 'update'])->middleware('permission:berita.update');
        Route::delete('/{id}', [NewsPostController::class, 'destroy'])->middleware('permission:berita.delete');
        Route::post('/{id}/publish', [NewsPostController::class, 'publish'])->middleware('permission:berita.publish');
        Route::post('/{id}/unpublish', [NewsPostController::class, 'unpublish'])->middleware('permission:berita.publish');
        Route::post('/{id}/schedule', [NewsPostController::class, 'schedule'])->middleware('permission:berita.publish');
        Route::post('/{id}/feature', [NewsPostController::class, 'feature'])->middleware('permission:berita.update');
        Route::post('/{id}/unfeature', [NewsPostController::class, 'unfeature'])->middleware('permission:berita.update');
    });

    // ========================================================================
    // BLOG POST MANAGEMENT (Admin)
    // ========================================================================
    Route::prefix('blog/posts')->middleware('permission:blog.view')->group(function () {
        Route::get('/', [BlogPostController::class, 'index']);
        Route::post('/', [BlogPostController::class, 'store'])->middleware('permission:blog.create');
        Route::get('/statistics', [BlogPostController::class, 'statistics']);
        Route::post('/bulk-delete', [BlogPostController::class, 'bulkDelete'])->middleware('permission:blog.delete');
        Route::post('/bulk-publish', [BlogPostController::class, 'bulkPublish'])->middleware('permission:blog.publish');
        Route::get('/{id}', [BlogPostController::class, 'show']); 
        Route::put('/{id}', [BlogPostController::class, 'update'])->middleware('permission:blog.update');
        Route::delete('/{id}', [BlogPostController::class, 'destroy'])->middleware('permission:blog.delete');
    });

    Route::prefix('blog/comments')->middleware('permission:komentar.view')->group(function () {
        Route::get('/', [BlogCommentController::class, 'index']);
        Route::post('/{id}/approve', [BlogCommentController::class, 'approve'])->middleware('permission:komentar.approve');
        Route::post('/{id}/reject', [BlogCommentController::class, 'reject'])->middleware('permission:komentar.approve');
        Route::delete('/{id}', [BlogCommentController::class, 'destroy'])->middleware('permission:komentar.delete');
    });

    // ========================================================================
    // NEWS CATEGORY MANAGEMENT
    // ========================================================================
    Route::prefix('news/categories')->middleware('permission:berita.view')->group(function () {
        Route::get('/', [NewsCategoryController::class, 'index']);
        Route::post('/', [NewsCategoryController::class, 'store'])->middleware('permission:berita.create');
        Route::get('/{id}', [NewsCategoryController::class, 'show']);
        Route::put('/{id}', [NewsCategoryController::class, 'update'])->middleware('permission:berita.update');
        Route::delete('/{id}', [NewsCategoryController::class, 'destroy'])->middleware('permission:berita.delete');
    });

    // ========================================================================
    // NEWS TAG MANAGEMENT
    // ========================================================================
    Route::prefix('news/tags')->middleware('permission:berita.view')->group(function () {
        Route::get('/', [NewsTagController::class, 'index']);
        Route::post('/', [NewsTagController::class, 'store'])->middleware('permission:berita.create');
        Route::get('/{id}', [NewsTagController::class, 'show']);
        Route::put('/{id}', [NewsTagController::class, 'update'])->middleware('permission:berita.update');
        Route::delete('/{id}', [NewsTagController::class, 'destroy'])->middleware('permission:berita.delete');
    });

    // ========================================================================
    // NEWS COMMENT MANAGEMENT
    // ========================================================================
    Route::prefix('news/comments')->middleware('permission:komentar.view')->group(function () {
        Route::get('/', [NewsCommentController::class, 'pending']);
        Route::get('/all', [NewsCommentController::class, 'index']);
        Route::get('/pending', [NewsCommentController::class, 'pending']);
        Route::post('/bulk-approve', [NewsCommentController::class, 'bulkApprove'])->middleware('permission:komentar.approve');
        Route::post('/bulk-reject', [NewsCommentController::class, 'bulkReject'])->middleware('permission:komentar.approve');
        Route::get('/{id}', [NewsCommentController::class, 'show']);
        Route::put('/{id}', [NewsCommentController::class, 'update'])->middleware('permission:komentar.approve');
        Route::delete('/{id}', [NewsCommentController::class, 'destroy'])->middleware('permission:komentar.delete');
        Route::post('/{id}/approve', [NewsCommentController::class, 'approve'])->middleware('permission:komentar.approve');
        Route::post('/{id}/reject', [NewsCommentController::class, 'reject'])->middleware('permission:komentar.approve');
    });

    // ========================================================================
    // NEWSLETTER MANAGEMENT (Admin)
    // ========================================================================
    Route::prefix('newsletter')->middleware('permission:newsletter.view')->group(function () {
        Route::get('/subscribers', [NewsletterAdminController::class, 'subscribers']);
        Route::get('/statistics', [NewsletterAdminController::class, 'statistics']);
        
        Route::get('/topics', [NewsletterAdminController::class, 'topics']);
        Route::post('/topics', [NewsletterAdminController::class, 'storeTopic'])->middleware('permission:newsletter.update');
        
        Route::get('/campaigns', [NewsletterAdminController::class, 'campaigns']);
        Route::post('/campaigns', [NewsletterAdminController::class, 'storeCampaign'])->middleware('permission:newsletter.update');
        Route::post('/campaigns/{id}/send', [NewsletterAdminController::class, 'sendCampaign'])->middleware('permission:newsletter.send');
        
        Route::get('/templates', [NewsletterAdminController::class, 'templates']);
        Route::post('/templates', [NewsletterAdminController::class, 'storeTemplate'])->middleware('permission:newsletter.update');
    });

    // ========================================================================
    // ANALYTICS MANAGEMENT
    // ========================================================================
    Route::prefix('analytics')->middleware('permission:statistik.view')->group(function () {
        Route::get('/overview', [AnalyticsController::class, 'getOverview']);
    });
});

// ============================================================================
// RATE LIMITED ROUTES
// ============================================================================

// Apply rate limiting to specific routes if needed
Route::middleware(['throttle:60,1'])->group(function () {
    // Routes that need strict rate limiting
});