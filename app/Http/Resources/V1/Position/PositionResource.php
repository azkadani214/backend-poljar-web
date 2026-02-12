<?php

namespace App\Http\Resources\V1\Position;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Division\DivisionResource;

class PositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'division_id' => $this->division_id,
            
            // Relationships
            'division' => new DivisionResource($this->whenLoaded('division')),
            
            // Counts
            'memberships_count' => $this->whenCounted('memberships'),
            
            // Computed
            'is_core_team' => $this->level >= 5,
            'is_staff' => $this->level < 5,
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}