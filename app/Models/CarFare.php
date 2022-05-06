<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarFare extends Model
{
    protected $table = 'car_fares';
    protected $guarded = [];

    public function carCategory()
    {
        return $this->belongsTo(CarCategory::class,'category_id','id');
    }
}
