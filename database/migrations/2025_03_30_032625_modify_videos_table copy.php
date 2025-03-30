<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Se establece el usuario por defecto a 24
        $defaultUserId = 24;
        $exists = DB::table('users')->where('id', $defaultUserId)->exists();
        if (!$exists) {
            throw new Exception("No existe ningún usuario con id = $defaultUserId. Crea primero este usuario o cambia el valor.");
        }
        
        // Actualizar los registros que tengan user_id nulo o que no existan en la tabla users.
        DB::table('videos')
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                                 ->from('users')
                                 ->whereColumn('users.id', 'videos.user_id');
                    });
            })
            ->update(['user_id' => $defaultUserId]);
        
        // Agregar la restricción foránea en la columna user_id.
        Schema::table('videos', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
