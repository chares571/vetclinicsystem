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
                $table->enum('type', ['vaccination', 'checkup', 'grooming'])->default('vaccination')->after('pet_id');
            });
        }

        DB::table('appointments')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'vaccination']);

        DB::table('appointments')
            ->where('type', 'medical')
            ->update(['type' => 'vaccination']);

        if (Schema::hasColumn('appointments', 'purpose')) {
            DB::table('appointments')
                ->whereNull('purpose')
                ->update(['purpose' => 'General Checkup']);
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('vaccination', 'checkup', 'grooming') NOT NULL DEFAULT 'vaccination'");
            DB::statement("ALTER TABLE appointments MODIFY purpose VARCHAR(255) NOT NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'type')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        DB::table('appointments')
            ->where('type', 'checkup')
            ->update(['type' => 'vaccination']);

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE appointments MODIFY type ENUM('vaccination', 'grooming') NOT NULL DEFAULT 'vaccination'");
        }
    }
};
