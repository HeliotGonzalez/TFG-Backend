<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Significado extends Model
{
    protected $table = 'significados';
    protected $fillable = ['descripcion', 'etiquetas', 'estado'];
    protected $casts = [
        'etiquetas' => 'array',
        'estado' => 'boolean'
    ];
}
