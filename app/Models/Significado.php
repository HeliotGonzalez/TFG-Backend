<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Etiqueta;

class Significado extends Model
{
    protected $table = 'significados';
    protected $fillable = ['descripcion', 'etiquetas', 'estado'];
    protected $casts = [
        'etiquetas' => 'array',
        'estado' => 'boolean'
    ];

    public function videos(){
        return $this->hasMany(Video::class);
    }
    
    public function highestVotedVideo()
    {
        return $this->hasOne(Video::class)->withCount('likes')->whereNotIn('corregido', [1, 3, 5])->orderBy('likes_count', 'desc');
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'significadoetiquetas', 'significado_id', 'etiqueta_id')->select('etiquetas.nombre');
    }

}
