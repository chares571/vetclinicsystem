<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hospitalizations')) {
            return;
        }

        Schema::create('hospitalizations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('admitted_date');
            $table->date('discharge_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'discharged'])->default('active');
            $table->text('medication_schedule')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('admitted_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalizations');
    }
};
