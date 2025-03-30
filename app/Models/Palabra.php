<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Palabra extends Model
{
    protected $table = 'palabras';
    protected $fillable = ['nombre', 'etiquetas', 'estado'];
    protected $casts = [
        'etiquetas' => 'array',
        'estado' => 'boolean'
    ];

    public function significado(){
        return $this->belongsTo(Significado::class, 'significado_id');
    }
}
