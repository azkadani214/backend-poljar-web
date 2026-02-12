<?php

namespace App\Http\Controllers\Api\V1\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Services\Position\PositionService;
use App\Http\Resources\V1\Position\PositionResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct(
        private PositionService $positionService
    ) {}

    /**
     * Get all positions
     * 
     * @group Position Management
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try {
            $positions = $this->positionService->getAllPositions();

            return ResponseHelper::success(
                PositionResource::collection($positions),
                'Positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create position
     * 
     * @group Position Management
     * @authenticated
     */
    public function store(StorePositionRequest $request): JsonResponse
    {
        try {
            $position = $this->positionService->createPosition($request->validated());

            return ResponseHelper::created(
                new PositionResource($position),
                'Position created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get position by ID
     * 
     * @group Position Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $position = $this->positionService->getPositionById($id);

            return ResponseHelper::success(
                new PositionResource($position),
                'Position retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update position
     * 
     * @group Position Management
     * @authenticated
     */
    public function update(UpdatePositionRequest $request, string $id): JsonResponse
    {
        try {
            $position = $this->positionService->updatePosition($id, $request->validated());

            return ResponseHelper::updated(
                new PositionResource($position),
                'Position updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete position
     * 
     * @group Position Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->positionService->deletePosition($id);

            return ResponseHelper::deleted('Position deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get positions by division
     * 
     * @group Position Management
     * @authenticated
     */
    public function byDivision(string $divisionId): JsonResponse
    {
        try {
            $positions = $this->positionService->getPositionsByDivision($divisionId);

            return ResponseHelper::success(
                PositionResource::collection($positions),
                'Positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get core team positions
     * 
     * @group Position Management
     * @authenticated
     */
    public function coreTeam(): JsonResponse
    {
        try {
            $positions = $this->positionService->getCoreTeamPositions();

            return ResponseHelper::success(
                PositionResource::collection($positions),
                'Core team positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get staff positions
     * 
     * @group Position Management
     * @authenticated
     */
    public function staff(): JsonResponse
    {
        try {
            $positions = $this->positionService->getStaffPositions();

            return ResponseHelper::success(
                PositionResource::collection($positions),
                'Staff positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get positions by level
     * 
     * @group Position Management
     * @authenticated
     */
    public function byLevel(Request $request): JsonResponse
    {
        $request->validate([
            'level' => 'required|integer|min:1|max:10'
        ]);

        try {
            $positions = $this->positionService->getPositionsByLevel($request->level);

            return ResponseHelper::success(
                PositionResource::collection($positions),
                'Positions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}

