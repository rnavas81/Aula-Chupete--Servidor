<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Rol extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'user_r_rol';
    protected $fillable = [
        'idUser',
        'idRol',
    ];
    public function users()
    {
        return $this->hasOne(User::class,'idUser','id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class,'idRol','id');
    }
}
