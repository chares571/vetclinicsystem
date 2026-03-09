<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fallbackUserId = DB::table('users')->where('role', 'admin')->value('id')
            ?: DB::table('users')->value('id');

        if ($fallbackUserId) {
            DB::table('pets')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
            DB::table('appointments')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
            DB::table('vaccinations')->whereNull('user_id')->update(['user_id' => $fallbackUserId]);
        }

        Schema::table('pets', function (Blueprint $table): void {
            $table->index(['user_id', 'pet_name']);
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->index(['user_id', 'appointment_date']);
            $table->index('status');
        });

        Schema::table('vaccinations', function (Blueprint $table): void {
            $table->index(['user_id', 'next_due_date']);
            $table->index('date_given');
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'pet_name']);
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'appointment_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('vaccinations', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'next_due_date']);
            $table->dropIndex(['date_given']);
        });
    }
};
