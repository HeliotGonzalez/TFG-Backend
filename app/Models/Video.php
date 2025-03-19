<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';
    protected $fillable = ['url', 'likes', 'dislikes', 'significado_id'];

    public function significado()
    {
        return $this->belongsTo(Significado::class);
    }
}
