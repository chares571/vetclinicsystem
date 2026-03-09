<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $promotedAdminIds = DB::table('users')
            ->whereIn('role', ['superadmin', 'master_admin', 'masteradmin'])
            ->pluck('id')
            ->all();

        if ($promotedAdminIds !== []) {
            DB::table('users')
                ->whereIn('id', $promotedAdminIds)
                ->update([
                    'role' => 'admin',
                    'must_change_password' => true,
                ]);
        }

        DB::table('users')
            ->where('role', 'admin')
            ->when($promotedAdminIds !== [], fn ($query) => $query->whereNotIn('id', $promotedAdminIds))
            ->update([
                'role' => 'veterinary_staff',
                'must_change_password' => false,
            ]);

        DB::table('users')
            ->whereNull('role')
            ->orWhereNotIn('role', ['admin', 'veterinary_staff'])
            ->update([
                'role' => 'veterinary_staff',
                'must_change_password' => false,
            ]);

        $hasAdmin = DB::table('users')->where('role', 'admin')->exists();

        if (! $hasAdmin) {
            $candidateId = DB::table('users')->orderBy('id')->value('id');

            if ($candidateId) {
                DB::table('users')
                    ->where('id', $candidateId)
                    ->update([
                        'role' => 'admin',
                        'must_change_password' => true,
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'veterinary_staff')
            ->update(['role' => 'admin']);
    }
};
