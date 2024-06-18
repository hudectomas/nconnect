<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlotAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'seminar_id',
        'time_slot_id',
    ];

    public function seminar()
    {
        return $this->belongsTo(Seminar::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
