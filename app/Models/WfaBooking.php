<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WfaBooking extends Model
{
    protected $fillable = [
        'dosen_id',
        'booking_date',
        'week_number',
        'year',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
