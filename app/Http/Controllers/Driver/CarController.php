<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\ApiController;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarModel;
use App\Models\Color;

class CarController extends ApiController
{
    public function carDetails()
    {
        $cars = Car::get();
        if ($cars) {
            return $this->result_ok('Cars', $cars);
        } else {
            return $this->result_fail('Something Went Wrong');
        }
    }

    public function carModels($id)
    {
        $models = CarModel::where('car_brand_id', $id)->get();
        if ($models) {
            return $this->result_ok('Car Models', $models);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function carCategory()
    {
        $category = CarCategory::get();
        if ($category) {
            return $this->result_ok('Car Categories', $category);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function color()
    {
        $color = Color::get();
        if ($color) {
            return $this->result_ok('Car Categories', $color);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function modelYear()
    {
        $year = range(date('Y'), 1900);
        if ($year) {
            return $this->result_ok('Car Categories', $year);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
