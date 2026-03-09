<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hospitalization_progress_notes')) {
            return;
        }

        Schema::create('hospitalization_progress_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hospitalization_id')
                ->constrained('hospitalizations')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('note_date');
            $table->text('notes');
            $table->timestamps();
            $table->index('note_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalization_progress_notes');
    }
};
