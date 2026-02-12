<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use App\Services\User\UserImportService;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Resources\V1\User\UserCollection;
use App\Http\Resources\V1\User\UserDetailResource;
use App\Http\Resources\V1\User\ActivityLogResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserImportService $importService
    ) {}

    /**
     * Get all users
     * 
     * @group User Management
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['search', 'status', 'division_id']);

            $users = $this->userService->getAllUsers($filters, $perPage);

            return ResponseHelper::paginated(
                $users,
                UserResource::class,
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create new user
     * 
     * @group User Management
     * @authenticated
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return ResponseHelper::created(
                new UserDetailResource($user),
                'User created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get user by ID
     * 
     * @group User Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            return ResponseHelper::success(
                new UserDetailResource($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update user
     * 
     * @group User Management
     * @authenticated
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return ResponseHelper::updated(
                new UserDetailResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete user
     * 
     * @group User Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return ResponseHelper::deleted('User deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Activate user
     * 
     * @group User Management
     * @authenticated
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $user = $this->userService->activateUser($id);

            return ResponseHelper::success(
                new UserResource($user),
                'User activated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Deactivate user
     * 
     * @group User Management
     * @authenticated
     */
    public function deactivate(string $id): JsonResponse
    {
        try {
            $user = $this->userService->deactivateUser($id);

            return ResponseHelper::success(
                new UserResource($user),
                'User deactivated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Search users
     * 
     * @group User Management
     * @authenticated
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:2'
        ]);

        try {
            $perPage = $request->input('per_page', 15);
            $users = $this->userService->searchUsers($request->keyword, $perPage);

            return ResponseHelper::paginated(
                $users,
                UserResource::class,
                'Users found'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get user statistics
     * 
     * @group User Management
     * @authenticated
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->userService->getUserStatistics();

            return ResponseHelper::success(
                $stats,
                'User statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk activate users
     * 
     * @group User Management
     * @authenticated
     */
    public function bulkActivate(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $count = $this->userService->bulkActivate($request->user_ids);

            return ResponseHelper::success(
                ['activated_count' => $count],
                "{$count} users activated successfully"
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Bulk deactivate users
     * 
     * @group User Management
     * @authenticated
     */
    public function bulkDeactivate(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $count = $this->userService->bulkDeactivate($request->user_ids);

            return ResponseHelper::success(
                ['deactivated_count' => $count],
                "{$count} users deactivated successfully"
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get user activity logs
     * 
     * @group User Management
     * @authenticated
     */
    public function activities(string $id, Request $request): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            $activities = $user->activityLogs()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($request->input('per_page', 10));

            return ResponseHelper::paginated(
                $activities,
                ActivityLogResource::class,
                'User activities retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get all user activity logs
     * 
     * @group User Management
     * @authenticated
     */
    public function allActivities(Request $request): JsonResponse
    {
        try {
            $activities = $this->userService->getAllActivities($request->input('per_page', 15));

            return ResponseHelper::paginated(
                $activities,
                ActivityLogResource::class,
                'All user activities retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Import users from CSV
     * 
     * @group User Management
     * @authenticated
     */
    public function import(Request $request): JsonResponse
    {
        try {
            // Check if importing from direct data (JSON)
            if ($request->has('data')) {
                $rows = $request->input('data');
                if (!is_array($rows)) {
                    throw new \Exception("Data format must be an array");
                }
                
                $summary = $this->importService->importFromArray($rows);
            } else {
                // Otherwise expect a file
                $request->validate([
                    'file' => 'required|file|mimes:csv,txt|max:2048'
                ]);
                
                $summary = $this->importService->importFromCsv($request->file('file'));
            }

            return ResponseHelper::success(
                $summary,
                'Import completed'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}