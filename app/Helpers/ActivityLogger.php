<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $action The action performed (e.g., 'created', 'updated', 'deleted', 'login')
     * @param string $module The module affected (e.g., 'User', 'News', 'Blog')
     * @param string $description A human-readable description of the activity
     * @param array|null $properties Additional data or changes
     * @param string|null $userId Specific user ID if not the authenticated user
     * @return ActivityLog
     */
    public static function log(string $action, string $module, string $description, ?array $properties = null, ?string $userId = null): ActivityLog
    {
        $userId = $userId ?? Auth::id();

        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
