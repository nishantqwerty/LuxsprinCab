<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Booking;
use Illuminate\Http\Request;
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
                'long2'             =>  $data['fare'],
                'ride_type'         =>  isset($data['fare']) ? $data['fare'] : 'private',
                'is_scheduled'      =>  isset($data['is_scheduled']) ? $data['is_scheduled'] : 0,
                'booking_time'      =>  date('Y-m-d H:i:s', strtotime($data['booking_time'])),
            ];
            $booking = Booking::create($details);
            if ($booking) {
                return $this->result_message('Booking Details Added.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
