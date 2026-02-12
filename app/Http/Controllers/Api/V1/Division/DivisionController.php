<?php

namespace App\Http\Controllers\Api\V1\Division;

use App\Http\Controllers\Controller;
use App\Http\Requests\Division\StoreDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use App\Services\Division\DivisionService;
use App\Http\Resources\V1\Division\DivisionResource;
use App\Http\Resources\V1\Division\DivisionDetailResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

class DivisionController extends Controller
{
    public function __construct(
        private DivisionService $divisionService
    ) {}

    /**
     * Get all divisions
     * 
     * @group Division Management
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try {
            $divisions = $this->divisionService->getAllDivisions();

            return ResponseHelper::success(
                DivisionResource::collection($divisions),
                'Divisions retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Create division
     * 
     * @group Division Management
     * @authenticated
     */
    public function store(StoreDivisionRequest $request): JsonResponse
    {
        try {
            $division = $this->divisionService->createDivision($request->validated());

            return ResponseHelper::created(
                new DivisionResource($division),
                'Division created successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get division by ID
     * 
     * @group Division Management
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        try {
            $division = $this->divisionService->getDivisionById($id);

            return ResponseHelper::success(
                new DivisionDetailResource($division),
                'Division retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\NotFoundException ? 404 : 500
            );
        }
    }

    /**
     * Update division
     * 
     * @group Division Management
     * @authenticated
     */
    public function update(UpdateDivisionRequest $request, string $id): JsonResponse
    {
        try {
            $division = $this->divisionService->updateDivision($id, $request->validated());

            return ResponseHelper::updated(
                new DivisionResource($division),
                'Division updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Delete division
     * 
     * @group Division Management
     * @authenticated
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->divisionService->deleteDivision($id);

            return ResponseHelper::deleted('Division deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Get division statistics
     * 
     * @group Division Management
     * @authenticated
     */
    public function statistics(string $id): JsonResponse
    {
        try {
            $stats = $this->divisionService->getDivisionStatistics($id);

            return ResponseHelper::success(
                $stats,
                'Division statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get division members
     * 
     * @group Division Management
     * @authenticated
     */
    public function members(string $id): JsonResponse
    {
        try {
            $members = $this->divisionService->getDivisionMembers($id);

            return ResponseHelper::success(
                $members,
                'Division members retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get divisions with active members
     * 
     * @group Division Management
     * @authenticated
     */
    public function withActiveMembers(): JsonResponse
    {
        try {
            $divisions = $this->divisionService->getDivisionsWithActiveMembers();

            return ResponseHelper::success(
                DivisionResource::collection($divisions),
                'Divisions with active members retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}