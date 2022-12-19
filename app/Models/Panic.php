<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panic extends Model
{
    protected $table = 'panic_management';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id')->with('carDetail');
    }
    
    public function booking(){
        return $this->belongsTo(Booking::class,'booking_id','id')->with('cardetails');

    }
    
}
