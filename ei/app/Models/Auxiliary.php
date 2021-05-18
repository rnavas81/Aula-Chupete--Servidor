<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxiliary extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $visible = ['id','value'];
}
