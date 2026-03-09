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

        $addType = ! Schema::hasColumn('appointments', 'type');
        $addGroomingServiceType = ! Schema::hasColumn('appointments', 'grooming_service_type');
        $addPreferredTime = ! Schema::hasColumn('appointments', 'preferred_time');
        $addNotes = ! Schema::hasColumn('appointments', 'notes');

        if ($addType || $addGroomingServiceType || $addPreferredTime || $addNotes) {
            Schema::table('appointments', function (Blueprint $table) use ($addType, $addGroomingServiceType, $addPreferredTime, $addNotes): void {
                if ($addType) {
                    $table->enum('type', ['medical', 'grooming'])->default('medical')->after('pet_id');
                }

                if ($addGroomingServiceType) {
                    $table->enum('grooming_service_type', ['basic_grooming', 'full_grooming', 'nail_trim', 'ear_cleaning'])
                        ->nullable()
                        ->after('purpose');
                }

                if ($addPreferredTime) {
                    $table->time('preferred_time')->nullable()->after('appointment_date');
                }

                if ($addNotes) {
                    $table->text('notes')->nullable()->after('grooming_service_type');
                }
            });
        }

        if (Schema::hasColumn('appointments', 'type')) {
            DB::table('appointments')
                ->whereNull('type')
                ->orWhere('type', '')
                ->update(['type' => 'medical']);
        }

        if (Schema::hasColumn('appointments', 'status')) {
            DB::table('appointments')
                ->whereRaw('LOWER(status) = ?', ['confirmed'])
                ->update(['status' => 'approved']);

            DB::table('appointments')
                ->whereRaw('LOWER(status) = ?', ['pending'])
                ->update(['status' => 'pending']);

            DB::table('appointments')
                ->whereRaw('LOWER(status) = ?', ['completed'])
                ->update(['status' => 'completed']);

            DB::table('appointments')
                ->whereRaw('LOWER(status) = ?', ['cancelled'])
                ->update(['status' => 'cancelled']);

            DB::table('appointments')
                ->whereRaw('LOWER(status) = ?', ['rejected'])
                ->update(['status' => 'rejected']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        $hasType = Schema::hasColumn('appointments', 'type');
        $hasGroomingServiceType = Schema::hasColumn('appointments', 'grooming_service_type');
        $hasPreferredTime = Schema::hasColumn('appointments', 'preferred_time');
        $hasNotes = Schema::hasColumn('appointments', 'notes');

        if (! $hasType && ! $hasGroomingServiceType && ! $hasPreferredTime && ! $hasNotes) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) use ($hasType, $hasGroomingServiceType, $hasPreferredTime, $hasNotes): void {
            if ($hasNotes) {
                $table->dropColumn('notes');
            }

            if ($hasGroomingServiceType) {
                $table->dropColumn('grooming_service_type');
            }

            if ($hasPreferredTime) {
                $table->dropColumn('preferred_time');
            }

            if ($hasType) {
                $table->dropColumn('type');
            }
        });
    }
};
