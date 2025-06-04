<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'banned',
        'name',
        'points',
        'last_challenge_made',
        'email',
        'password',
        'role_id',
        'username',
        'proveniencia',
        'descripcion',
        'otp_code', 
        'otp_expires_at',
        're_get_password_token',
        're_get_password_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function diccionario()
    {
        return $this->hasMany(Diccionario::class);
    }
    
    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }
}
