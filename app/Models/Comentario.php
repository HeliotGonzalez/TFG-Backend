<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';
    protected $fillable = ['contenido', 'video_id', 'user_id', 'comentario_padre_id'];

    public function comentario_padre(){
        return $this->belongsTo(Comentario::class, 'comentario_padre_id');
    }

    // This gives every single answer (respuesta) to a coment
    public function respuestas(){
        return $this->hasMany(Comentario::class, 'comentario_padre_id');
    }
}
