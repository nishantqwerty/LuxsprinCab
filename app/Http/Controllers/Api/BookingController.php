<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\CancelCommission;
use App\Models\CancellationEarning;
use App\Models\CarDetail;
use App\Models\Route;
use App\Models\Stops;
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
                        print_r($sen);
                    } elseif ($driver->device_type == 'ios') {
                        $sen = $this->sendNotificationAndroid($msgdata);
                        print_r($sen);
                    }
                }
                return $this->result_ok('Nearby Drivers', ['booking_id' => $booking->id, 'drivers' => $details]);
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
            $booking = Booking::where('id', $data['booking_id'])->where('user_id', auth('api')->user()->id)->first();
            // return $data['cancellation_reason'];
            if ($booking) {
                if ($booking->is_cancelled == BOOKING_CANCEL) {
                    return $this->result_message('Booking Already Cancelled.');
                } else {
                    $user = User::find(auth('api')->user()->id);
                    $driver = User::find($booking->driver_id);
                    $arr = $data['cancellation_reason'];
                    $res = implode(",", $arr);
                    $booking->update([
                        'is_cancelled'  =>  BOOKING_CANCEL,
                        'cancelled_by'  =>  USER,
                        'cancellation_reasons' => $res
                    ]);
                    $msgDataDriver = [
                        'id'    =>  $driver->id,
                        'device_token'    =>  $driver->device_token,
                        'message'   =>  'Booking has been cancelled.'
                    ];
                    $msgDataUser = [
                        'id'    =>  $user->id,
                        'device_token'    =>  $user->device_token,
                        'message'   =>  'Booking has been cancelled.'
                    ];
                    $this->sendCancelNotificationDriver($msgDataDriver);
                    $this->sendCancelNotificationUser($msgDataUser);
                    return $this->result_message('Booking has been cancelled successfully.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function getFare($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            return $this->result_ok($booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function sharingCab(Request $request)
    {
        $request = $request->all();
        $validator = Validator::make($request, [
            'source'            =>  'required',
            'destination'       =>  'required',
            'car_category_id'   =>  'required',
            'fare'              =>  'required',
            'lat1'              =>  'required',
            'long1'             =>  'required',
            'lat2'              =>  'required',
            'long2'             =>  'required',
            'seats'             =>  'required'
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
                'source'            =>  $request['source'],
                'destination'       =>  $request['destination'],
                'user_id'           =>  auth('api')->user()->id,
                'car_category_id'   =>  $request['car_category_id'],
                'fare'              =>  $request['fare'],
                'lat1'              =>  $request['lat1'],
                'long1'             =>  $request['long1'],
                'lat2'              =>  $request['lat2'],
                'long2'             =>  $request['long2'],
                'ride_type'         =>  isset($request['ride_type']) ? $request['ride_type'] : 'private',
                'booking_time'      =>  date('Y-m-d H:i'),
            ];

            $auth_user = User::find(auth('api')->user()->id);
            $lat1 = $request['lat1'];
            $long1 = $request['long1'];
            $users = User::where('user_role', DRIVER)->where('is_online', DRIVER_ONLINE)->where('cab-mode', 'sharing')->where('in-ride', DRIVER_NOT_RIDING)->where('is_logged_in', DRIVER_LOG_IN)->get();
            $data = [];
            foreach ($users as $user) {
                $seats = CarDetail::where('user_id', $user->id)->first();
                if ($seats->capacity >= $request['seats']) {
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
            $booking = Booking::where('id', $data['booking_id'])->where('user_id', auth('api')->user()->id)->first();
            if ($booking) {
                $driver = User::find($booking->driver_id);
                if ($driver) {
                    $msgData = [
                        'id'    =>  $driver->id,
                        'device_token'  =>  $driver->device_token,
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
        $booking = Booking::where('user_id', auth('api')->user()->id)->where('is_completed', RIDE_COMPLETE)->with(['driver', 'details', 'cardetails'])->orderBy('created_at', 'DESC')->get();
        if ($booking) {
            return $this->result_ok('Completed Booking', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function upcomingTrips()
    {
        // $booking = Booking::where('user_id', auth('api')->user()->id)->where('is_scheduled', RIDE_SCHEDULED)->where('driver_id', 0)->get();
        $booking = Booking::where('is_scheduled', RIDE_SCHEDULED)->where('driver_id', 0)->orderBy('created_at', 'DESC')->get();
        // return $booking;die;
        if ($booking) {
            return $this->result_ok('Upcoming Booking', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function ongoingTrips()
    {
        $booking = Booking::where('user_id', auth('api')->user()->id)->where('is_completed', RIDE_ONGOING)->with(['driver', 'details', 'cardetails'])->get();
        if ($booking) {
            return $this->result_ok('Ongoing Booking', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function tripDetails($bookingId)
    {
        $booking = Booking::where('id', $bookingId)->where('user_id', auth('api')->user()->id)->with(['driver', 'details', 'cardetails', 'rating'])->first();
        if ($booking) {
            return $this->result_ok('Trip Detail', $booking);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function cancelTrip(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'bookingId'     =>  'required',
            'fare'          =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $cance_charge = CancelCommission::first();
            if ($cance_charge) {
                $booking = Booking::where('id', $data['bookingId'])->where('user_id', auth('api')->user()->id)->first();
                if ($booking) {
                    $arr = $data['cancellation_reason'];
                    $res = implode(",", $arr);
                    $booking->update([
                        'is_cancelled' => BOOKING_CANCEL,
                        'cancelled_by' => USER,
                        'cancellation_reasons' => $res
                    ]);
                    $user = User::find(auth('api')->user()->id);
                    if ($user) {
                        $user->update([
                            'outstanding_amount' => $data['fare'] * ($cance_charge['commission'] / 100)
                        ]);

                        $cancellation = CancellationEarning::create([
                            'driver_id' => $booking->driver_id,
                            'user_id'   => auth('api')->user()->id,
                            'amount' => $data['fare'] * ($cance_charge['commission'] / 100),
                        ]);
                    } else {
                        return $this->result_message('No User Found');
                    }
                } else {
                    return $this->result_message('No Booking Found');
                }
                return $this->result_message('Booking Cancelled');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
