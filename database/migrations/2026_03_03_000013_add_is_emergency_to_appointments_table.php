<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments') || Schema::hasColumn('appointments', 'is_emergency')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table): void {
            $table->boolean('is_emergency')
                ->default(false)
                ->after('status');
            $table->index('is_emergency');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'is_emergency')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex(['is_emergency']);
            $table->dropColumn('is_emergency');
        });
    }
};
