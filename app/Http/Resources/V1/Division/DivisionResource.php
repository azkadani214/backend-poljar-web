<?php

namespace App\Http\Resources\V1\Division;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Position\PositionResource;

class DivisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            
            // Relationships
            'positions' => PositionResource::collection($this->whenLoaded('positions')),
            
            // Counts
            'positions_count' => $this->whenCounted('positions'),
            'users_count' => $this->whenCounted('users'),
            'memberships_count' => $this->whenCounted('memberships'),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}