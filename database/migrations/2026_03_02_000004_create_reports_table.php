<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('scope', ['daily', 'weekly', 'monthly', 'custom'])->default('monthly');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scope']);
            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
