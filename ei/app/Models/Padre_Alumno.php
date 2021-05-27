<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Padre_Alumno extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'padres_r_alumnos';
    protected $fillable = [
        'idUser',
        'idAlumno',
    ];
    protected $hidden = [
        'idUser',
        'idAlumno',
    ];
    public function padre()
    {
        return $this->hasOne(User::class,'id','idUser');
    }
    public function alumno()
    {
        return $this->hasOne(Alumno::class,'id','idAlumno');
    }
}
