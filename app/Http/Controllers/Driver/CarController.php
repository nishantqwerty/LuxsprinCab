<?php

namespace App\Http\Controllers\Driver;

use App\Models\Car;
use App\Models\User;
use App\Models\Color;
use App\Models\Booking;
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

    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)->where('driver_id', auth('api')->user()->id)->first();
        if ($booking) {
            if ($booking->is_cancelled == BOOKING_CANCEL) {
                return $this->result_message('Booking Already Cancelled.');
            } else {
                $booking->update([
                    'is_cancelled'  =>  BOOKING_CANCEL,
                    'cancelled_by'  =>  DRIVER,
                ]);
                return $this->result_message('Booking has been cancelled successfully.');
            }
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function rideStart(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required',
            'ride_status'   =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $booking = Booking::find($data['booking_id']);
            if ($booking) {
                $driver = User::find(auth('api')->user()->id);
                $user   =   User::find($booking->user_id);
                if ($driver) {
                    if ($data['status'] == RIDE_START) {
                        $driver->update([
                            'in-ride'   =>  DRIVER_RIDING
                        ]);
                        $userdata = [
                            'id'    =>  $user->id,
                            'message'   =>  'Ride Started',
                            'device_token'  =>  $user->device_token,
                        ];
                        $driverdata = [
                            'id'    =>  $driver->id,
                            'message'   =>  'Ride Started',
                            'device_token'  =>  $driver->device_token,
                        ];
                        $ride = $this->rideStartNotification($userdata);
                        $driverride = $this->rideStartNotification($driverdata);
                        return $this->result_message('Ride started.');
                    } elseif ($data['status'] == RIDE_END) {
                        $driver->update([
                            'in-ride'   =>  DRIVER_NOT_RIDING
                        ]);
                        $booking->update([
                            'is_completed'  =>  RIDE_COMPLETE
                        ]);
                        $userdata = [
                            'id'    =>  $user->id,
                            'message'   =>  'Your ride is compeleted',
                            'device_token'  =>  $user->device_token,
                        ];
                        $driverdata = [
                            'id'    =>  $driver->id,
                            'message'   =>  'Ride Completed',
                            'device_token'  =>  $driver->device_token,
                        ];
                        $ride = $this->rideStartNotification($userdata);
                        $driverride = $this->rideStartNotification($driverdata);
                        return $this->result_message('Ride ended.');
                    }
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
