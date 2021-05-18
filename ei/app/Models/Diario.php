<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diario extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'idAula',
        'date',
        'title',
        'content',
    ];
    protected $hidden = [
        'idAula'
    ];
    public function entradas()
    {
        return $this->hasMany(Diario_Entrada::class,'idDiario','id');
    }
    public function aula()
    {
        return $this->hasOne(Aula::class,'idAula','id');
    }
}
