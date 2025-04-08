<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';
    protected $fillable = ['url', 'likes', 'dislikes', 'significado_id', 'user_id'];


    public function significado()
    {
        return $this->belongsTo(Significado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diccionario()
    {
        return $this->hasMany(Diccionario::class);
    }
}
