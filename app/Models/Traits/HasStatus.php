<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    /**
     * Scope to get only active records
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive records
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if record is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if record is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Activate record
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate record
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }
}