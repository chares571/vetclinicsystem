<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medicines')) {
            return;
        }

        Schema::create('medicines', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->date('expiration_date')->nullable();
            $table->string('supplier')->nullable();
            $table->unsignedInteger('low_stock_threshold')->default(10);
            $table->timestamps();
            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
