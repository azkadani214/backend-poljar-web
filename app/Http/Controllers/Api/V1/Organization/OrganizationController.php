<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Position;
use App\Models\Membership;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    /**
     * Get organizational chart data (tree structure)
     * 
     * @group Organization Management
     * @authenticated
     */
    public function chart(): JsonResponse
    {
        try {
            // Get all divisions with their positions and active members
            $divisions = Division::with(['positions.memberships.user' => function ($query) {
                $query->where('is_active', true);
            }])->get();

            $chartData = [
                'id' => 'org-root',
                'name' => 'Politeknik Negeri Malang', // Or get from config
                'type' => 'organization',
                'children' => $divisions->map(function ($division) {
                    return [
                        'id' => 'div-' . $division->id,
                        'name' => $division->name,
                        'type' => 'division',
                        'children' => $division->positions->map(function ($position) {
                            return [
                                'id' => 'pos-' . $position->id,
                                'name' => $position->name,
                                'type' => 'position',
                                'level' => $position->level,
                                'children' => $position->memberships->map(function ($membership) {
                                    return [
                                        'id' => 'mem-' . $membership->id,
                                        'name' => $membership->user->name,
                                        'type' => 'member',
                                        'photo' => $membership->user->avatar_url,
                                        'is_active' => $membership->is_active,
                                    ];
                                })
                            ];
                        })
                    ];
                })
            ];

            return ResponseHelper::success($chartData, 'Organizational chart data retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
