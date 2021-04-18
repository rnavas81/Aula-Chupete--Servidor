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
}
