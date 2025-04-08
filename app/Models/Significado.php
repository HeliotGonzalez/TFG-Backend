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

    public function videos(){
        return $this->hasMany(Video::class);
    }
    
    public function highestVotedVideo()
    {
        return $this->hasOne(Video::class)
                    ->withCount('likes')
                    ->orderBy('likes_count', 'desc');
    }

}
