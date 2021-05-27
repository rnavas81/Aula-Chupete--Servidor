<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'owner',
        'active',
    ];
    protected $hidden = [
        'active',
        'owner',
        'updated_at',
        'created_at',
    ];
    public function dias()
    {
        return $this->hasMany(Menu_Dia::class,'idMenu','id');
    }
    public function owner()
    {
        return $this->hasOne(Aula::class,'id','owner');
    }
}
