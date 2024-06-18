<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'website_url',
    ];

    /**
     * Get the user that owns the sponsor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
