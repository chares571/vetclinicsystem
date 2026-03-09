<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    public function log(
        ?User $user,
        string $eventType,
        string $entityType,
        ?int $entityId,
        string $description,
        array $context = []
    ): void {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        AuditLog::query()->create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'context' => $context === [] ? null : $context,
            'created_at' => now(),
        ]);
    }
}
