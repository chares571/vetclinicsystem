<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pets') || Schema::hasColumn('pets', 'sex')) {
            return;
        }

        Schema::table('pets', function (Blueprint $table): void {
            $table->enum('sex', ['male', 'female'])->nullable()->after('breed');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pets') || ! Schema::hasColumn('pets', 'sex')) {
            return;
        }

        Schema::table('pets', function (Blueprint $table): void {
            $table->dropColumn('sex');
        });
    }
};
