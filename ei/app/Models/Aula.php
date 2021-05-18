<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'idUser',
        'default',
        'age_range',
        'year',
    ];
    protected $hidden = [
        'active',
        'updated_at',
        'created_at',
        'idUser',
    ];
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'idUser');
    }
    public function alumnos()
    {
        return $this->hasMany(Aula_Alumno::class,'idAula','id');
    }
    public function age()
    {
        return $this->hasOne(Auxiliary::class,'id','age_range')->where('type',1);
    }
    public function diarios($date)
    {
        return $this->hasMany(Diario::class,'idAula','id')->where('date',$date);
    }
}
