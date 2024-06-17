<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seminar extends Model
{
    use HasFactory;

    protected $table = 'talks'; // This line specifies the custom table name

    protected $fillable = [
        'title',
        'speaker_id',
        'short_description',
        'long_description',
    ];

    public function speaker()
    {
        return $this->belongsTo(Speaker::class); // This line defines the relationship
    }
}
