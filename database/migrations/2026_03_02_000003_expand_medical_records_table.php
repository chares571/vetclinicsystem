<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table): void {
            if (! Schema::hasColumn('medical_records', 'pet_id')) {
                $table->foreignId('pet_id')
                    ->nullable()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->after('id');
            }

            if (! Schema::hasColumn('medical_records', 'complaint')) {
                $table->text('complaint')->nullable()->after('pet_id');
            }

            if (! Schema::hasColumn('medical_records', 'diagnosis')) {
                $table->text('diagnosis')->nullable()->after('complaint');
            }

            if (! Schema::hasColumn('medical_records', 'treatment')) {
                $table->text('treatment')->nullable()->after('diagnosis');
            }

            if (! Schema::hasColumn('medical_records', 'visit_date')) {
                $table->date('visit_date')->nullable()->after('treatment');
                $table->index('visit_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table): void {
            if (Schema::hasColumn('medical_records', 'visit_date')) {
                $table->dropIndex(['visit_date']);
            }

            if (Schema::hasColumn('medical_records', 'pet_id')) {
                $table->dropConstrainedForeignId('pet_id');
            }

            $columns = array_filter([
                Schema::hasColumn('medical_records', 'complaint') ? 'complaint' : null,
                Schema::hasColumn('medical_records', 'diagnosis') ? 'diagnosis' : null,
                Schema::hasColumn('medical_records', 'treatment') ? 'treatment' : null,
                Schema::hasColumn('medical_records', 'visit_date') ? 'visit_date' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
