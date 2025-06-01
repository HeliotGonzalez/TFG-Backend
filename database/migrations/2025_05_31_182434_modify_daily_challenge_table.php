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
        Schema::table('daily_challenge', function (Blueprint $table) {
            $table->foreignId('video_id')->constrained('videos')->after('palabra_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_challenge', function (Blueprint $table) {
            $table->dropColumn('video_id');
        });
    }
};
