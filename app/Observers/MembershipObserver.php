<?php

namespace App\Observers;

use App\Models\Membership;
use Illuminate\Support\Facades\Log;

class MembershipObserver
{
    /**
     * Handle the Membership "created" event.
     */
    public function created(Membership $membership): void
    {
        Log::info('Membership created', [
            'membership_id' => $membership->id,
            'user_id' => $membership->user_id,
            'division_id' => $membership->division_id,
            'position_id' => $membership->position_id,
        ]);
    }

    /**
     * Handle the Membership "updated" event.
     */
    public function updated(Membership $membership): void
    {
        // Log activation changes
        if ($membership->isDirty('is_active')) {
            Log::info('Membership activation changed', [
                'membership_id' => $membership->id,
                'user_id' => $membership->user_id,
                'is_active' => $membership->is_active,
            ]);
        }
    }

    /**
     * Handle the Membership "deleted" event.
     */
    public function deleted(Membership $membership): void
    {
        Log::info('Membership deleted', [
            'membership_id' => $membership->id,
            'user_id' => $membership->user_id,
        ]);
    }
}