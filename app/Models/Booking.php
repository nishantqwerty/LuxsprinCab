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

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function details(){
        return $this->belongsTo(DriverDocument::class,'driver_id','user_id');

    }

    public function rating(){
        return $this->belongsTo(Rating::class,'driver_id','driver_id');

    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'id','booking_id');

    }

    public function cardetails(){
        return $this->belongsTo(CarDetail::class,'driver_id','user_id')->with(['category','brand','brandModel']);

    }
}
