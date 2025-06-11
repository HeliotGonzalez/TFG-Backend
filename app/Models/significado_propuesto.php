<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class significado_propuesto extends Model
{
    protected $table = 'significados_propuestos';

    protected $fillable = [
        'user_id',
        'palabra',
        'descripcion_antigua',
        'descripcion_propuesta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
