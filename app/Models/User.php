<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'status',
        'phone',
        'gender',
        'birth_date',
        'address',
        'bio',
        'quotes',
        'website',
        'social_links',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'social_links' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->id = Str::uuid();
        });
    }

    /** RELATIONS */



protected $appends = ['avatar_url', 'is_admin', 'role_name', 'role_display'];

public function getIsAdminAttribute(): bool
{
    return $this->isAdmin();
}

public function getAvatarUrlAttribute(): string
{
    if ($this->photo && Storage::disk('public')->exists($this->photo)) {
        return asset('storage/' . $this->photo);
    }

    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
}

public function getRoleNameAttribute(): ?string
{
    return $this->roles->first()?->name;
}

public function getRoleDisplayAttribute(): ?string
{
    return $this->roles->first()?->label;
}


    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function divisions()
    {
        return $this->belongsToMany(Division::class, 'memberships');
    }

    /**
     * Check if user is an admin (has core team position)
     * Core team = positions with level <= 2
     */
    public function isAdmin(): bool
    {
        // 1. Check if user has admin roles
        $adminRoles = ['administrator', 'admin', 'penulis'];
        
        if ($this->relationLoaded('roles')) {
            if ($this->roles->whereIn('name', $adminRoles)->count() > 0) {
                return true;
            }
        } else if ($this->roles()->whereIn('name', $adminRoles)->exists()) {
            return true;
        }

        // 2. Check core team membership
        return $this->memberships()
            ->whereHas('position', function ($query) {
                $query->where('level', '<=', 2);
            })
            ->where('is_active', true)
            ->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    /**
     * Sync roles for the user (replace existing)
     */
    public function syncRoles(array $roleNames): void
    {
        $ids = Role::whereIn('name', $roleNames)->pluck('id');
        $this->roles()->sync($ids);
    }
    
    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        // ONLY 'administrator' role gets full bypass
        if ($this->roles->contains('name', 'administrator')) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    /**
     * Check if user has any active membership
     */
    public function hasActiveMembership(): bool
    {
        return $this->memberships()->where('is_active', true)->exists();
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
