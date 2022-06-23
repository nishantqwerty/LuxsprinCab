<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $guarded = [];

    public function driver(){
        return $this->belongsTo(User::class,'driver_id','id');
    }
}
