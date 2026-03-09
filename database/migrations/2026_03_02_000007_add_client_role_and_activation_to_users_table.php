<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        DB::table('users')
            ->whereIn('role', ['superadmin', 'master_admin', 'masteradmin'])
            ->update(['role' => 'admin']);

        DB::table('users')
            ->whereIn('role', ['pet_owner', 'owner', 'customer'])
            ->update(['role' => 'client']);

        DB::table('users')
            ->whereNull('role')
            ->orWhereNotIn('role', ['admin', 'veterinary_staff', 'client'])
            ->update(['role' => 'client']);

        DB::table('users')
            ->whereNull('is_active')
            ->update(['is_active' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropIndex(['is_active']);
                $table->dropColumn('is_active');
            }
        });

        DB::table('users')
            ->where('role', 'client')
            ->update(['role' => 'veterinary_staff']);
    }
};
