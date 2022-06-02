<?php

namespace App\Http\Controllers\Driver;

use App\Models\Car;
use App\Models\User;
use App\Models\Color;
use App\Models\CarModel;
use App\Models\CarCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

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

    public function cabMode(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'mode'  => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user = User::find(auth('api')->user()->id);
            if ($user) {
                $user->update([
                    'cab_mode'  =>  strtolower($data['mode'])
                ]);
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function getCabMode()
    {
        $user = User::select('cab-mode')->where('id', auth('api')->user()->id)->first();
        if ($user) {
            return $this->result_ok('User Cab Mode', $user);
        } else {
            return $this->result_fail('SOmething Went Wrong.');
        }
    }
}
