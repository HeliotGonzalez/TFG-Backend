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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->foreignId('role_id')->after('id')->constrained('role');
            $table->string('proveniencia')->after('role_id');
            $table->string('descricion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('proveniencia');
            $table->dropColumn('descricion');
        });
    }
};
