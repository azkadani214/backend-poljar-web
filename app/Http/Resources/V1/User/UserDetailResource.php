<?php

namespace App\Http\Resources\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Division\DivisionResource;
use App\Http\Resources\V1\Membership\MembershipResource;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo' => $this->photo,
            'photo_url' => $this->avatar_url,
            'status' => $this->status,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'bio' => $this->bio,
            'website' => $this->website,
            'social_links' => $this->social_links,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            
            // Full relationships
            'divisions' => DivisionResource::collection($this->whenLoaded('divisions')),
            'memberships' => MembershipResource::collection($this->whenLoaded('memberships')),
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'label' => $role->label,
                    'permissions' => $role->permissions->pluck('name'),
                ]);
            }),
            
            // Statistics
            'statistics' => [
                'total_memberships' => $this->memberships()->count(),
                'active_memberships' => $this->memberships()->where('is_active', true)->count(),
                'total_divisions' => $this->divisions()->count(),
            ],
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}