<?php

// app/Models/Gallery.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['name', 'years'];

    public static $rules = [
        'name' => 'required|string|max:255',
        'years' => 'required|array',
        'years.*' => 'integer|min:1900|max:9999',
        // Ďalšie pravidlá validácie
    ];

}
