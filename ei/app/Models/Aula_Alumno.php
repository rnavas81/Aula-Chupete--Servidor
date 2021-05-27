<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula_Alumno extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'aula_r_alumnos';
    protected $fillable = [
        'idAula',
        'idAlumno',
    ];
    public function aula()
    {
        return $this->hasOne(Aula::class,'id','idAula');
    }
    public function alumnos()
    {
        return $this->belongsTo(Alumno::class,'idAlumno','id');
    }
}
