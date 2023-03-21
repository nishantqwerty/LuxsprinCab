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
use App\Models\CarDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
                    'cab-mode'  =>  strtolower($data['mode'])
                ]);
                return $this->result_message('Cab Mode Updated.');
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

    public function cancelBooking(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $booking = Booking::where('id', $data['booking_id'])->where('driver_id', auth('api')->user()->id)->first();
            if ($booking) {
                if ($booking->is_cancelled == BOOKING_CANCEL) {
                    return $this->result_message('Booking Already Cancelled.');
                } else {
                    $driver = User::find(auth('api')->user()->id);
                    $user = User::find($booking->user_id);
                    $arr = $data['cancellation_reason'];
                    $res = implode(",", $arr);
                    $booking->update([
                        'is_cancelled'  =>  BOOKING_CANCEL,
                        'cancelled_by'  =>  DRIVER,
                        'cancellation_reasons' => $res
                    ]);
                    // if($booking->ride_type == 'sharing'){
                    //         $car_detail = CarDetail::where('user_id',$booking->driver_id)->first();
                    //         $car_detail->update([
                    //             'available_seats'   =>  $car_detail->available_seats + $booking->seats
                    //         ]); 
                    // }
                    $msgDataDriver = [
                        'id'    =>  $driver->id,
                        'device_token'  =>  $driver->device_token,
                        'message'   =>  'Your booking has been cancelled'
                    ];
                    $msgDataUser = [
                        'id'    =>  $user->id,
                        'device_token'  =>  $user->device_token,
                        'message'   =>  'Your booking has been cancelled'
                    ];
                    $this->sendCancelNotificationDriver($msgDataDriver);
                    $user = $this->sendCancelNotificationUser($msgDataUser);
                    return $this->result_message('Booking has been cancelled successfully.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
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
                    if ($data['ride_status'] == RIDE_START) {
                        // Log::channel('sharing')->info('ride start.', ['RIDE_START' => RIDE_START]);
                        $driver->update([
                            'in-ride'   =>  DRIVER_RIDING
                        ]);
                        $booking->update([
                            'is_completed'  =>  RIDE_ONGOING
                        ]);
                        //   if($booking->ride_type == 'sharing'){
                        //     $car_detail = CarDetail::where('user_id',$booking->driver_id)->first();
                        //     $car_detail->update([
                        //         'available_seats'   =>  $car_detail->available_seats - $booking->seats
                        //     ]);
                        // }
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
                    } elseif ($data['ride_status'] == RIDE_END) {
                        $driver->update([
                            'in-ride'   =>  DRIVER_NOT_RIDING
                        ]);
                        $booking->update([
                            'is_completed'  =>  RIDE_COMPLETE
                        ]);
                        $booking = Booking::find($data['booking_id']);
                        // if($booking->ride_type == 'sharing'){
                        //     $car_detail = CarDetail::where('user_id',$booking->driver_id)->first();
                        //     Log::channel('sharing')->info('Driver details.', ['driver' => $booking->driver_id]);
                        //     Log::channel('sharing')->info('Booking Seats.', ['bookingseats' => $booking->seats]);
                        //     Log::channel('sharing')->info('Available Seats.', ['availseats' => $car_detail->available_seats]);
                        //     $car_detail->update([
                        //         'available_seats'   =>  $car_detail->available_seats + $booking->seats

                        //     ]); 
                        //     Log::channel('sharing')->info('Available Seats Update.', ['availseats' => $car_detail->available_seats]);
                        //     $car_details = CarDetail::where('user_id',$booking->driver_id)->first();
                        //     if($car_details->available_seats > $car_details->capacity){
                        //         $car_details->update([
                        //         'available_seats'   =>  $car_details->capacity

                        //         ]); 
                        //         Log::channel('sharing')->info('Available Seats Update Case 1.', ['availseats' => $car_details->available_seats]);
                        //     }
                        //     $car_detaill = CarDetail::where('user_id',$booking->driver_id)->first();
                        //     if($car_detaill->available_seats ==  $car_detaill->capacity) {
                        //          $driver->update([
                        //             'in-ride'   =>  DRIVER_NOT_RIDING
                        //         ]);
                        //         Log::channel('sharing')->info('Available Seats Update Case 2.', ['availseats' => $car_detaill->available_seats]);
                        //     } 
                        //     elseif($car_detaill->available_seats > $car_detaill->capacity){
                        //          $driver->update([
                        //             'in-ride'   =>  DRIVER_NOT_RIDING
                        //         ]);
                        //     }
                        // }
                        // else{
                        //     Log::channel('sharing')->info('driver not riding.');
                        //     $driver->update([
                        //                 'in-ride'   =>  DRIVER_NOT_RIDING
                        //             ]);
                        // }

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
                        $ride = $this->rideEndNotification($userdata);
                        $driverride = $this->rideEndNotification($driverdata);
                        return $this->result_message('Ride ended.');
                    }
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function sendNotify(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $booking = Booking::where('id', $data['booking_id'])->where('driver_id', auth('api')->user()->id)->first();
            if ($booking) {
                $User = User::find($booking->user_id);
                if ($User) {
                    $msgData = [
                        'id'    =>  $User->id,
                        'device_token'  =>  $User->device_token,
                        'message'   =>  'Driver Reached'
                    ];
                    $this->sendReachNotification($msgData);
                    return $this->result_ok('Notification Sent.');
                } else {
                    return $this->result_fail('No driver found.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function sendCustomNotification(Request $request)
    {
        
        $data = $request->all();
        $validator = Validator::make($data, [
            'message'    =>  'required',
            'booking_id' =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $booking = Booking::where('id', $data['booking_id'])->where('driver_id', auth('api')->user()->id)->first();
            if ($booking) {
                $user = User::find($booking->user_id);
                if ($user) {
                    $msgData = [
                        'id'    =>  $user->id,
                        'device_token'  =>  $user->device_token,
                        'message'   =>  $data['message']
                    ];
                    $this->sendNotification($msgData);
                    return $this->result_message('Message Sent.');
                } else {
                    return $this->result_fail('No user found.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function completedTrips()
    {
        $bookings = Booking::where('driver_id', auth('api')->user()->id)->where('is_completed', RIDE_COMPLETE)->with(['user', 'cardetails'])->orderBy('created_at', 'DESC')->get();
        
        if ($bookings) {
            //  foreach($bookings as $booking);
            //  {    
            //     $booking['user']['value'] = $booking['user']['image'];
            //     unset($booking['user']['image']);

            //          $booking['user']['image'] =  asset('storage/images/'.$booking['user']['value']);
                
            //         $booktemp[] = $booking;

               
            // }
               foreach($bookings as $booking) {
                if($booking['user']){
                    $booking['user']['value'] = (isset($booking['user']['image'])) ? $booking['user']['image'] : ""; 
                }
               }
                 foreach($bookings as $booking) {
                    if($booking['user'])
                    unset($booking['user']['image']);
               }
                 foreach($bookings as $booking) {
                    if($booking['user'])
                    $booking['user']['image'] = (@$booking['user']['value']) ? asset('storage/images/'.$booking['user']['value']) : ""; 
               }
                 foreach($bookings as $booking) {
                    if($booking['user'])
                        unset($booking['user']['value']);
                }
                return $this->result_ok('Completed Booking', $bookings);
        }
         else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function upcomingTrips()
    {
        $booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_scheduled', RIDE_SCHEDULED)->orderBy('created_at', 'DESC')->get();
        if ($booking) {
            return $this->result_ok('Upcoming Booking', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function ongoingTrips()
    {
        $bookings = Booking::where('driver_id', auth('api')->user()->id)->where('is_completed', RIDE_ONGOING)->with(['user', 'cardetails'])->orderBy('created_at', 'DESC')->get();
        if ($bookings) {
             foreach($bookings as $booking)
             {    
                $booking['user']['old_image'] = $booking['user']['image'];
                unset($booking['user']['image']);
                $booking['user']['image'] =  asset('storage/images/'.$booking['user']['old_image']);
               
            }
                return $this->result_ok('Completed Booking', $bookings);
        }
         else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function tripDetails($bookingId)
    {
        $booking = Booking::where('id', $bookingId)->where('driver_id', auth('api')->user()->id)->with(['user', 'details', 'cardetails', 'rating'])->first();

         if ($booking) {
                if($booking['user']){
                    $booking['user']['value'] = (isset($booking['user']['image'])) ? $booking['user']['image'] : ""; 
                    unset($booking['user']['image']);
                     $booking['user']['image'] = (@$booking['user']['value']) ? asset('storage/images/'.$booking['user']['value']) : ""; 
                      unset($booking['user']['value']);
                }
               
                
            return $this->result_ok('Trip Detail', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    //     if ($booking) {
    //         return $this->result_ok('Trip Detail', $booking);
    //     } else {
    //         return $this->result_fail('Something Went Wrong.');
    //     }
    // }
}
}
