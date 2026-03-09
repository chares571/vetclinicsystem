<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if (! Schema::hasColumn('appointments', 'type')) {
            Schema::table('appointments', function (Blueprint $table): void {
                $table->enum('type', ['vaccination', 'grooming'])->default('vaccination')->after('pet_id');
            });

            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('medical', 'vaccination', 'grooming') NULL DEFAULT 'vaccination'");
        }

        DB::table('appointments')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'vaccination']);

        DB::table('appointments')
            ->where('type', 'medical')
            ->update(['type' => 'vaccination']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('vaccination', 'grooming') NOT NULL DEFAULT 'vaccination'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'type')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('medical', 'vaccination', 'grooming') NULL DEFAULT 'medical'");
        }

        DB::table('appointments')
            ->where('type', 'vaccination')
            ->update(['type' => 'medical']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('medical', 'grooming') NOT NULL DEFAULT 'medical'");
        }
    }
};
