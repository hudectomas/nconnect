<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_name',
        'capacity',
        'start_time',
        'end_time',
        'user_agent',
        'payload',
        'last_activity',
        'uuid',
        'user_pole'

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    /**
     * Používatelia prihlásení na danú session.
     */

}
