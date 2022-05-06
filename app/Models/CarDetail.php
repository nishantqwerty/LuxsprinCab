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

    public function category(){
        return $this->belongsTo(CarCategory::class,'category_id','id');
    }

    public function brand(){
        return $this->belongsTo(Car::class,'brand','id');
    }

    public function brandModel(){
        return $this->belongsTo(CarModel::class,'brand_model','id');
    }

}
