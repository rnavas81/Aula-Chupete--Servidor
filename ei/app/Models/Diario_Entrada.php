<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diario_Entrada extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'diario_entradas';
    protected $primaryKey = ['idDiario','idAlumno'];

    protected $fillable = [
        'idDiario',
        'idAlumno',
        'activity',
        'food',
        'behaviour',
        'sphincters',
        'absence',
    ];
    protected $hidden = [
        'idDiario',
    ];

    public function diario()
    {
        return $this->belongsTo(Diario::class,'idDiario','id');
    }

}
