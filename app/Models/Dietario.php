<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dietario extends Model
{
    use HasFactory;
    protected $fillable = [
        'idAula',
        'date',
        'breakfast',
        'breakfast_allergens',
        'lunch',
        'lunch_allergens',
        'desert',
        'desert_allergens',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function aula()
    {
        return $this->hasOne(Aula::class,'idAula','id');
    }
}
