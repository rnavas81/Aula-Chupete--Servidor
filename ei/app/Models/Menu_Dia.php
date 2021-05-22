<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu_Dia extends Model
{
    use HasFactory;
    protected $table = 'menu_dias';
    protected $fillable = [
        'idMenu',
        'dia',
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
    public function menu()
    {
        return $this->hasOne(Menu::class,'idMenu','id');
    }
}
