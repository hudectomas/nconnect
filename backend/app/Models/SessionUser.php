<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id', // Pridanie user_id
        'session_name',
        'user_name',
        'capacity'
    ];
}
