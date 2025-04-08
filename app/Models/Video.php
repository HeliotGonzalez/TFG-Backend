<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Significado;
use App\Models\User;
use App\Models\Diccionario;
use App\Models\Reporte;
use App\Models\UserVideo;

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

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    public function userVideos()
    {
        return $this->hasMany(UserVideo::class);
    }

    public function likes()
    {
        return $this->hasMany(UserVideo::class)->where('action', 'like');
    }
}
