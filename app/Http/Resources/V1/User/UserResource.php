<?php
// ============================================================================
// FILE 61: app/Http/Resources/V1/User/UserResource.php
// ============================================================================

namespace App\Http\Resources\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\V1\Division\DivisionResource;
use App\Http\Resources\V1\Membership\MembershipResource;
use Illuminate\Support\Facades\Hash;

class UserResource extends JsonResource
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
            'avatar_url' => $this->avatar_url, // For consistency with frontend
            'status' => $this->status,
            'is_admin' => $this->is_admin,
            'role_name' => $this->roles->first()?->name,
            'role_display' => $this->roles->first()?->label,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'bio' => $this->bio,
            'quotes' => $this->quotes,
            'website' => $this->website,
            'social_links' => $this->social_links,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            
            // Relationships
            'divisions' => DivisionResource::collection($this->whenLoaded('divisions')),
            'memberships' => MembershipResource::collection($this->whenLoaded('memberships')),
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'label' => $role->label,
                ]);
            }),
                        'permissions' => $this->relationLoaded('roles') 
                ? $this->roles->flatMap(function($role) {
                    return $role->permissions->pluck('name');
                })->unique()->values()->toArray()
                : [],
            'is_default_password' => Hash::check('password', $this->password),
            
            // Counts
            'memberships_count' => $this->whenCounted('memberships'),
            'divisions_count' => $this->whenCounted('divisions'),
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}