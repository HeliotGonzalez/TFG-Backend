<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class significadoEtiqueta extends Model
{
    protected $table = 'significadoetiquetas';
    protected $fillable = ['significado_id', 'etiqueta_id', 'created_at'];


}
