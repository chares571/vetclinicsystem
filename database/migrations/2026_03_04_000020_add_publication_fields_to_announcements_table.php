<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->boolean('is_pinned')->default(false)->after('image_path');
            $table->enum('priority', ['normal', 'important'])->default('normal')->after('is_pinned');
            $table->timestamp('publish_at')->nullable()->after('priority');
            $table->timestamp('expires_at')->nullable()->after('publish_at');

            $table->index(['is_pinned', 'priority']);
            $table->index('publish_at');
            $table->index('expires_at');
        });

        DB::table('announcements')
            ->whereNull('publish_at')
            ->update(['publish_at' => DB::raw('created_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->dropIndex(['is_pinned', 'priority']);
            $table->dropIndex(['publish_at']);
            $table->dropIndex(['expires_at']);

            $table->dropColumn([
                'is_pinned',
                'priority',
                'publish_at',
                'expires_at',
            ]);
        });
    }
};
