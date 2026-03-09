<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments') || Schema::hasColumn('appointments', 'staff_id')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table): void {
            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('user_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'staff_id')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('staff_id');
        });
    }
};
