<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokenAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Token Gate Routes (No middleware - public access)
Route::get('/admin/token', [TokenAuthController::class, 'showTokenForm'])
    ->name('admin.token.login');

Route::post('/admin/token', [TokenAuthController::class, 'verifyToken'])
    ->name('admin.token.verify');

Route::post('/admin/token/logout', [TokenAuthController::class, 'logoutToken'])
    ->name('admin.token.logout');

// Admin Login (Protected by token gate)
Route::middleware(['admin.token'])->group(function () {
    
    // Redirect /admin to admin dashboard
    Route::get('/admin', function () {
        return redirect('/admin/dashboard');
    });
    
    // Additional custom admin routes can be added here
});
