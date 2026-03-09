<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('medical_records') || Schema::hasColumn('medical_records', 'user_id')) {
            return;
        }

        Schema::table('medical_records', function (Blueprint $table): void {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('medical_records') || ! Schema::hasColumn('medical_records', 'user_id')) {
            return;
        }

        Schema::table('medical_records', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
