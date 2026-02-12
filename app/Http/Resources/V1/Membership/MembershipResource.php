<?php

namespace App\Http\Resources\V1\Membership;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Resources\V1\Division\DivisionResource;
use App\Http\Resources\V1\Position\PositionResource;

class MembershipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'division_id' => $this->division_id,
            'position_id' => $this->position_id,
            'is_active' => $this->is_active,
            'period' => $this->period,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'division' => new DivisionResource($this->whenLoaded('division')),
            'position' => new PositionResource($this->whenLoaded('position')),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
