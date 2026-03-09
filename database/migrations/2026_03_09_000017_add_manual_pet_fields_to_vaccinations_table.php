<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasPetName = Schema::hasColumn('vaccinations', 'pet_name');
        $hasOwnerName = Schema::hasColumn('vaccinations', 'owner_name');
        $hasContactNumber = Schema::hasColumn('vaccinations', 'contact_number');

        if (! $hasPetName || ! $hasOwnerName || ! $hasContactNumber) {
            Schema::table('vaccinations', function (Blueprint $table) use ($hasPetName, $hasOwnerName, $hasContactNumber): void {
                if (! $hasPetName) {
                    $table->string('pet_name')->nullable()->after('pet_id');
                }

                if (! $hasOwnerName) {
                    $table->string('owner_name')->nullable()->after('pet_name');
                }

                if (! $hasContactNumber) {
                    $table->string('contact_number', 50)->nullable()->after('owner_name');
                }
            });
        }

        $vaccinations = DB::table('vaccinations')
            ->select('id', 'pet_id', 'pet_name', 'owner_name', 'contact_number')
            ->get();

        if ($vaccinations->isNotEmpty()) {
            $petIds = $vaccinations->pluck('pet_id')->filter()->unique()->values();
            $petsById = $petIds->isEmpty()
                ? collect()
                : DB::table('pets')
                    ->whereIn('id', $petIds)
                    ->get(['id', 'pet_name', 'owner_name', 'contact_number'])
                    ->keyBy('id');

            foreach ($vaccinations as $vaccination) {
                $pet = $vaccination->pet_id ? ($petsById[$vaccination->pet_id] ?? null) : null;
                $petName = trim((string) $vaccination->pet_name);
                $ownerName = trim((string) $vaccination->owner_name);
                $contactNumber = trim((string) $vaccination->contact_number);

                DB::table('vaccinations')
                    ->where('id', $vaccination->id)
                    ->update([
                        'pet_name' => $petName !== '' ? $petName : ($pet->pet_name ?? null),
                        'owner_name' => $ownerName !== '' ? $ownerName : ($pet->owner_name ?? null),
                        'contact_number' => $contactNumber !== '' ? $contactNumber : ($pet->contact_number ?? null),
                    ]);
            }
        }

        if (Schema::hasColumn('vaccinations', 'pet_id')) {
            Schema::table('vaccinations', function (Blueprint $table): void {
                $table->dropForeign(['pet_id']);
            });

            Schema::table('vaccinations', function (Blueprint $table): void {
                $table->unsignedBigInteger('pet_id')->nullable()->change();
            });

            Schema::table('vaccinations', function (Blueprint $table): void {
                $table->foreign('pet_id')->references('id')->on('pets')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $hasContactNumber = Schema::hasColumn('vaccinations', 'contact_number');
        $hasOwnerName = Schema::hasColumn('vaccinations', 'owner_name');
        $hasPetName = Schema::hasColumn('vaccinations', 'pet_name');

        if (! $hasContactNumber && ! $hasOwnerName && ! $hasPetName) {
            return;
        }

        Schema::table('vaccinations', function (Blueprint $table) use ($hasContactNumber, $hasOwnerName, $hasPetName): void {
            if ($hasContactNumber) {
                $table->dropColumn('contact_number');
            }

            if ($hasOwnerName) {
                $table->dropColumn('owner_name');
            }

            if ($hasPetName) {
                $table->dropColumn('pet_name');
            }
        });
    }
};
