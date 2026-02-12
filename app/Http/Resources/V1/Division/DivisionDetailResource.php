<?php

namespace App\Http\Resources\V1\Division;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Position\PositionResource;
use App\Http\Resources\V1\Membership\MembershipResource;

class DivisionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            
            // Full relationships
            'positions' => PositionResource::collection($this->whenLoaded('positions')),
            'memberships' => MembershipResource::collection($this->whenLoaded('memberships')),
            
            // Statistics
            'statistics' => [
                'total_positions' => $this->positions()->count(),
                'total_members' => $this->users()->count(),
                'active_memberships' => $this->memberships()->where('is_active', true)->count(),
                'inactive_memberships' => $this->memberships()->where('is_active', false)->count(),
            ],
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}