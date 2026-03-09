<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('vaccinations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
    $table->string('vaccine_name');
    $table->date('date_given');
    $table->date('next_due_date');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
