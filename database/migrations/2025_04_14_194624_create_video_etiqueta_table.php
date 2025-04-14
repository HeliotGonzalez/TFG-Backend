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
        Schema::create('significadoEtiquetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('significado_id')->constrained('significados');
            $table->foreignId('etiqueta_id')->constrained('etiquetas');
            $table->timestamps();

            $table->unique(['significado_id', 'etiqueta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('significadoEtiquetas');
    }
};
