<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'lastname',
        'birthday',
        'allergies',
        'intolerances',
        'diseases',
    ];
    protected $hidden = [
        'active',
        'updated_at',
        'created_at',
    ];
    public function aulas()
    {
        return $this->hasMany(Aula_Alumno::class,'idAlumno','id');
    }
}
