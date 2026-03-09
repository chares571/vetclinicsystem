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
            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password');
            }
        });

        DB::table('users')
            ->whereIn('role', ['master_admin', 'masteradmin'])
            ->update(['role' => 'admin']);

        DB::table('users')
            ->whereNull('role')
            ->orWhereNotIn('role', ['admin', 'veterinary_staff'])
            ->update(['role' => 'veterinary_staff']);

        DB::table('users')
            ->where('role', 'admin')
            ->update(['must_change_password' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }

            $table->dropIndex(['role']);
        });

        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'master_admin']);
    }
};
