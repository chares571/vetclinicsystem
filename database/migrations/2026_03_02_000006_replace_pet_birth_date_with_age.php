<?php

use Carbon\Carbon;
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

        Schema::table('pets', function (Blueprint $table): void {
            if (! Schema::hasColumn('pets', 'age')) {
                $table->unsignedSmallInteger('age')->nullable()->after('breed');
            }
        });

        if (Schema::hasColumn('pets', 'birth_date')) {
            DB::table('pets')
                ->whereNull('age')
                ->whereNotNull('birth_date')
                ->orderBy('id')
                ->chunkById(200, function ($pets): void {
                    foreach ($pets as $pet) {
                        try {
                            $age = Carbon::parse($pet->birth_date)->age;
                        } catch (\Throwable) {
                            $age = null;
                        }

                        DB::table('pets')
                            ->where('id', $pet->id)
                            ->update(['age' => $age]);
                    }
                });

            Schema::table('pets', function (Blueprint $table): void {
                $table->dropColumn('birth_date');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pets')) {
            return;
        }

        Schema::table('pets', function (Blueprint $table): void {
            if (! Schema::hasColumn('pets', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('breed');
            }
        });

        if (Schema::hasColumn('pets', 'age')) {
            DB::table('pets')
                ->whereNull('birth_date')
                ->whereNotNull('age')
                ->orderBy('id')
                ->chunkById(200, function ($pets): void {
                    foreach ($pets as $pet) {
                        $birthDate = Carbon::now()
                            ->subYears((int) $pet->age)
                            ->toDateString();

                        DB::table('pets')
                            ->where('id', $pet->id)
                            ->update(['birth_date' => $birthDate]);
                    }
                });

            Schema::table('pets', function (Blueprint $table): void {
                $table->dropColumn('age');
            });
        }
    }
};
