<?php

namespace App\Http\Controllers\Api\V1\Membership;

use App\Http\Controllers\Controller;
use App\Http\Requests\Membership\StoreMembershipRequest;
use App\Http\Requests\Membership\UpdateMembershipRequest;
use App\Services\Membership\MembershipService;
use App\Http\Resources\V1\Membership\MembershipResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function __construct(
        private MembershipService $membershipService
    ) {}

    /**
     * Get all memberships
     * 
     * @group Membership Management
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['is_active']);

            $memberships = $this->membershipService->getAllMemberships($filters, $perPage);

            return ResponseHelper::paginated(
                $memberships,
                MembershipResource::class,
                'Memberships retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create membership
     * 
     * @group Membership Management
     * @authenticated
     */
    public function store(StoreMembershipRequest $request): JsonResponse
    {
        try {
            $membership = $this->membershipService->createMembership($request->validated());

            return ResponseHelper::created(
                new MembershipResource($membership),
                'Membership created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get membership by ID
     * 
     * @group Membership Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $membership = $this->membershipService->getMembershipById($id);

            return ResponseHelper::success(
                new MembershipResource($membership),
                'Membership retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update membership
     * 
     * @group Membership Management
     * @authenticated
     */
    public function update(UpdateMembershipRequest $request, string $id): JsonResponse
    {
        try {
            $membership = $this->membershipService->updateMembership($id, $request->validated());

            return ResponseHelper::updated(
                new MembershipResource($membership),
                'Membership updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete membership
     * 
     * @group Membership Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->membershipService->deleteMembership($id);

            return ResponseHelper::deleted('Membership deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Activate membership
     * 
     * @group Membership Management
     * @authenticated
     */
    public function activate(string $id): JsonResponse
    {
        try {
            $membership = $this->membershipService->activateMembership($id);

            return ResponseHelper::success(
                new MembershipResource($membership),
                'Membership activated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Deactivate membership
     * 
     * @group Membership Management
     * @authenticated
     */
    public function deactivate(string $id): JsonResponse
    {
        try {
            $membership = $this->membershipService->deactivateMembership($id);

            return ResponseHelper::success(
                new MembershipResource($membership),
                'Membership deactivated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get memberships by user
     * 
     * @group Membership Management
     * @authenticated
     */
    public function byUser(string $userId): JsonResponse
    {
        try {
            $memberships = $this->membershipService->getMembershipsByUser($userId);

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Memberships retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get memberships by division
     * 
     * @group Membership Management
     * @authenticated
     */
    public function byDivision(string $divisionId): JsonResponse
    {
        try {
            $memberships = $this->membershipService->getMembershipsByDivision($divisionId);

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Memberships retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get memberships by position
     * 
     * @group Membership Management
     * @authenticated
     */
    public function byPosition(string $positionId): JsonResponse
    {
        try {
            $memberships = $this->membershipService->getMembershipsByPosition($positionId);

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Memberships retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get core team
     * 
     * @group Membership Management
     */
    public function coreTeam(): JsonResponse
    {
        try {
            $memberships = $this->membershipService->getCoreTeam();

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Core team retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get staff
     * 
     * @group Membership Management
     */
    public function staff(): JsonResponse
    {
        try {
            $memberships = $this->membershipService->getStaff();

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Staff retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get memberships by period
     * 
     * @group Membership Management
     * @authenticated
     */
    public function byPeriod(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'required|string|max:50'
        ]);

        try {
            $memberships = $this->membershipService->getMembershipsByPeriod($request->period);

            return ResponseHelper::success(
                MembershipResource::collection($memberships),
                'Memberships retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
