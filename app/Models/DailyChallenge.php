<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
    protected $table = 'daily_challenge';

    protected $fillable = ['palabra_id', 'video_id'];

    public function palabra() { return $this->belongsTo(Palabra::class); }
    public function video()   { return $this->belongsTo(Video::class);   }
}
