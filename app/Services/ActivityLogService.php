<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ActivityLogService
{
    public function log(?User $user, string $action, string $description): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
