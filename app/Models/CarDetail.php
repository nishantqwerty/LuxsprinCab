<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarDetail extends Model
{
    protected $table = 'car_details';
    protected $guarded = [];

    public function documents(){
        return $this->belongsTo(DriverDocument::class,'user_id','user_id');
    }
}
