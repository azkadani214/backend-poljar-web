<?php

namespace App\Observers;

use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ActivityLogger::log(
            'created',
            'User',
            "Pengguna baru {$user->name} ({$user->email}) telah terdaftar.",
            $user->toArray()
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log status changes
        if ($user->isDirty('status')) {
            $oldStatus = $user->getOriginal('status');
            $newStatus = $user->status;
            
            ActivityLogger::log(
                'updated',
                'User',
                "Status pengguna {$user->name} diubah dari {$oldStatus} menjadi {$newStatus}.",
                [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ActivityLogger::log(
            'deleted',
            'User',
            "Pengguna {$user->name} ({$user->email}) telah dihapus dari sistem."
        );
    }
}