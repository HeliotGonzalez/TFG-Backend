<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Significado;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';
    protected $fillable = ['nombre', 'created_at'];

    public function significados()
    {
        return $this->belongsToMany(Significado::class, 'significadoetiquetas', 'etiqueta_id', 'significado_id');
    }

}
