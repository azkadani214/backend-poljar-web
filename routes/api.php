<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Polinema Mengajar API',
        'version' => config('api.version', 'v1'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

// API V1 Routes
Route::prefix('v1')
    ->middleware(['api', 'api.version'])
    ->group(base_path('routes/api_v1.php'));

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'available_versions' => ['v1'],
    ], 404);
});