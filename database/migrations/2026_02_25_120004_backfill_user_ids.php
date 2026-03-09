<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('users')
            ->whereIn('role', ['admin', 'superadmin', 'master_admin', 'masteradmin'])
            ->value('id');
        $fallbackUserId = $adminId ?: DB::table('users')->value('id');

        if (!$fallbackUserId) {
            return;
        }

        DB::table('pets')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
        DB::table('appointments')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
        DB::table('vaccinations')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
    }

    public function down(): void
    {
        // Non-destructive backfill; no rollback.
    }
};
