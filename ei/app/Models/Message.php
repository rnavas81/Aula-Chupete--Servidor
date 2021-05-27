<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user',
        'message',
        'read',
    ];
    protected $hidden = [
        'updated_at',
        'created_at',
        'user',
    ];
}
