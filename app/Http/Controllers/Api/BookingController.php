<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class BookingController extends ApiController
{
    public function saveBooking(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'source'            =>  'required',
            'destination'       =>  'required',
            'car_category_id'   =>  'required',
            'fare'              =>  'required',
            'lat1'              =>  'required',
            'long1'             =>  'required',
            'lat2'              =>  'required',
            'long2'             =>  'required',
            'booking_time'      =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $details    =   [
                'source'            =>  $data['source'],
                'destination'       =>  $data['destination'],
                'user_id'           =>  auth('api')->user()->id,
                'car_category_id'   =>  $data['car_category_id'],
                'fare'              =>  $data['fare'],
                'lat1'              =>  $data['lat1'],
                'long1'             =>  $data['long1'],
                'lat2'              =>  $data['lat2'],
                'long2'             =>  $data['long2'],
                'ride_type'         =>  isset($data['ride_type']) ? $data['ride_type'] : 'private',
                'is_scheduled'      =>  isset($data['is_scheduled']) ? $data['is_scheduled'] : 0,
                'booking_time'      =>  date('Y-m-d H:i', strtotime($data['booking_time'])),
            ];
            $booking = Booking::create($details);
            if ($booking) {
                return $this->result_message('Booking Details Added.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function createBooking(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'source'            =>  'required',
            'destination'       =>  'required',
            'car_category_id'   =>  'required',
            'fare'              =>  'required',
            'lat1'              =>  'required',
            'long1'             =>  'required',
            'lat2'              =>  'required',
            'long2'             =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $booking_data = [
                'source'            =>  $data['source'],
                'destination'       =>  $data['destination'],
                'user_id'           =>  auth('api')->user()->id,
                'car_category_id'   =>  $data['car_category_id'],
                'fare'              =>  $data['fare'],
                'lat1'              =>  $data['lat1'],
                'long1'             =>  $data['long1'],
                'lat2'              =>  $data['lat2'],
                'long2'             =>  $data['long2'],
                'ride_type'         =>  isset($data['ride_type']) ? $data['ride_type'] : 'private',
                'booking_time'      =>  date('Y-m-d H:i')
            ];

            $auth_user = User::find(auth('api')->user()->id);
            $lat1 = $data['lat1'];
            $long1 = $data['long1'];
            $users = User::where('user_role', DRIVER)->where('is_online', DRIVER_ONLINE)->where('cab-mode', 'private')->where('in-ride', DRIVER_NOT_RIDING)->where('is_logged_in', DRIVER_LOG_IN)->get();
            $data = [];
            foreach ($users as $user) {
                $lat2 = $user->lat;
                $long2 = $user->long;
                $theta = $long1 - $long2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                // return $dist;
                //in KMs
                $miles = $dist * 60 * 1.1515 * 1.609344;
                // return $miles;
                if ($miles <= env('DISTANCE')) {
                    $data['id'][]      =   $user->id;
                    $data['name'][]    =   $user->name;
                    $data['lat'][]     =   $lat2;
                    $data['long'][]    =   $long2;
                    $data['dist'][]    =   $miles;
                }
                $unit = strtoupper('N');

                // if ($unit == "K") {
                //     return ($miles * 1.609344);
                // } else if ($unit == "N") {
                //     return ($miles * 0.8684);
                // } else {
                //     return $miles;
                // }
            }
            if (!empty($data['id'])) {
                foreach ($data['id'] as $key_id => $id) {
                    $details[$key_id]['id'] = $id;
                }
            }
            if (!empty($data['name'])) {
                foreach ($data['name'] as $key => $name) {
                    $details[$key]['driver_name'] = $name;
                }
            }
            if (!empty($data['lat'])) {
                foreach ($data['lat'] as $key1 => $lat) {
                    $details[$key1]['lat'] = $lat;
                }
            }
            if (!empty($data['long'])) {
                foreach ($data['long'] as $key2 => $long) {
                    $details[$key2]['long'] = $long;
                }
            }
            if (!empty($data['dist'])) {
                foreach ($data['dist'] as $key3 => $dist) {
                    $details[$key3]['dist'] = round($dist, 1) . ' km';
                }
            }
            if (!empty($details)) {
                $booking = Booking::create($booking_data);
                foreach ($details as $notify) {
                    $driver = User::find($notify['id']);
                    $user = User::find(auth('api')->user()->id);
                    // return $user;
                    $msgdata = [
                        'id'    =>  $user->id,
                        'device_token'  =>  $driver->device_token,
                        'message'       =>  'Booking Request',
                        'user_image'    =>  !empty($user->image)   ?   $user->image  :   'no_image',
                        'user_name'     =>  !empty($user->name)   ?   $user->name  :   'NULL',
                        'booking_id'    =>  $booking->id,
                        'booking_data'  =>  $booking_data,
                        'distance'      =>  $notify['dist']
                    ];
                    if ($driver->device_type == 'android') {
                        $sen = $this->sendNotificationAndroid($msgdata);
                    } elseif ($driver->device_type == 'ios') {
                        $sen = $this->sendNotificationAndroid($msgdata);
                    }
                }
                return $this->result_ok('Nearby Drivers', ['boking_id' => $booking->id, 'drivers' => $details]);
            } else {
                return $this->result_fail('No nearby driver found.');
            }
        }
    }

    public function updateLatLong(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'lat'   =>  'required',
            'long'  =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $latlong = User::find(auth('api')->user()->id);
            if ($latlong) {
                $latlong->update([
                    'lat'   => $data['lat'],
                    'long'   => $data['long'],
                ]);
                return $this->result_message('Lat Long Updated Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)->where('user_id', auth('api')->user()->id)->first();
        if ($booking) {
            if ($booking->is_cancelled == BOOKING_CANCEL) {
                return $this->result_message('Booking Already Cancelled.');
            } else {
                $booking->update([
                    'is_cancelled'  =>  BOOKING_CANCEL,
                    'cancelled_by'  =>  USER,
                ]);
                return $this->result_message('Booking has been cancelled successfully.');
            }
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function sharingCab(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'pickup_location'   =>  'required',
            'drop_location'     =>  'required',
            'pickup_lat'        =>  'required',
            'pickup_long'       =>  'required',
            'drop_lat'          =>  'required',
            'drop_long'         =>  'required',
            'car_category_id'   =>  'required',
        ]);
    }
}
