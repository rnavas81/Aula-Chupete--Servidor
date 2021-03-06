<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'activated',
        'activated_token',
        'contact',
        'owner',
    ];

    protected $hidden = [
        'updated_at',
        'password',
        'remember_token',
        'activated',
        'activated_token',
        'blocked',
        'tries',
        'email_verified_at',
        'created_at',
        'owner',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function roles()
    {
        return $this->hasMany(User_Rol::class,'idUser','id');
    }
    public function aulas()
    {
        return $this->hasMany(Aula::class,'idUser','id');
    }
}
