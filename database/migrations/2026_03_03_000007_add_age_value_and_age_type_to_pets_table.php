<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pets')) {
            return;
        }

        $addAgeValue = ! Schema::hasColumn('pets', 'age_value');
        $addAgeType = ! Schema::hasColumn('pets', 'age_type');

        if ($addAgeValue || $addAgeType) {
            Schema::table('pets', function (Blueprint $table) use ($addAgeValue, $addAgeType): void {
                if ($addAgeValue) {
                    $table->unsignedSmallInteger('age_value')->nullable()->after('breed');
                }

                if ($addAgeType) {
                    $table->enum('age_type', ['month', 'year'])->nullable()->default('year')->after('age_value');
                }
            });
        }

        if (Schema::hasColumn('pets', 'age') && Schema::hasColumn('pets', 'age_value')) {
            DB::table('pets')
                ->whereNull('age_value')
                ->whereNotNull('age')
                ->update([
                    'age_value' => DB::raw('age'),
                    'age_type' => 'year',
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pets')) {
            return;
        }

        $hasAgeValue = Schema::hasColumn('pets', 'age_value');
        $hasAgeType = Schema::hasColumn('pets', 'age_type');

        if (! $hasAgeValue && ! $hasAgeType) {
            return;
        }

        Schema::table('pets', function (Blueprint $table) use ($hasAgeValue, $hasAgeType): void {
            if ($hasAgeType) {
                $table->dropColumn('age_type');
            }

            if ($hasAgeValue) {
                $table->dropColumn('age_value');
            }
        });
    }
};
