<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $fillable = [
        'owner',
        'name',
        'lastname',
        'gender',
        'birthday',
        'allergies',
        'intolerances',
        'diseases',
        'observations',
    ];
    protected $hidden = [
        'owner',
        'active',
        'updated_at',
        'created_at',
    ];
    public function aulas()
    {
        return $this->hasMany(Aula_Alumno::class,'idAlumno','id');
    }
    public function padres(){
        return $this->hasMany(Padre_Alumno::class,'idAlumno','id');
    }
}
