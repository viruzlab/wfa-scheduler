<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WfaBooking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_date',
        'week_number',
        'year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
