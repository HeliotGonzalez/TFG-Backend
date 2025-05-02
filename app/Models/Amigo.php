<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Amigo extends Model
{
    protected $table = 'amigos';

    protected $fillable = [
        'user_id',
        'amigo_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function amigo()
    {
        return $this->belongsTo(User::class, 'amigo_id');
    }

}
