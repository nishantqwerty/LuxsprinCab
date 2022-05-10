<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class LocationController extends ApiController
{

    function get_coordinates($city, $street, $province)
    {
        $address = urlencode($city . ',' . $street . ',' . $province);
        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Poland";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);
        $status = $response_a->status;

        if ($status == 'ZERO_RESULTS') {
            return FALSE;
        } else {
            $return = array('lat' => $response_a->results[0]->geometry->location->lat, 'long' => $long = $response_a->results[0]->geometry->location->lng);
            return $return;
        }
    }

    // function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    // public function GetDrivingDistance(Request $request)
    // {
    //     $auth_user = User::find(auth('api')->user()->id);
    //     $lat1 = $auth_user->lat;
    //     $long1 = $auth_user->long;
    //     $users = User::where('user_role', DRIVER)->get();
    //     $response_a = [];
    //     foreach ($users as $user) {
    //         $lat2 = $user->lat;
    //         $long2 = $user->long;
    //         $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=" . env('PLACES_API') . "&origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL";
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //         $response = curl_exec($ch);
    //         curl_close($ch);
    //         $response_a[] = json_decode($response, true);
    //         return $response['rows'];
    //         // $dist = $response_a[0]['rows'][0]['elements'][0]['distance']['text'];
    //         // foreach($response_a as $res){
    //         //     $calculate_distance = $res['rows'][0]['elements'][0]['distance']['text'];
    //         //     if($calculate_distance == 2 ){
    //         //         $abc['lat'][] =   $user->lat;
    //         //         $abc['long'][] =   $user->long;
    //         //     }
    //         // }
    //         // $time[] = $response_a['rows'][0]['elements'][0]['duration']['text'];
    //     }
    //     return $abc;
    //     foreach($calculate_distance as $dist){
    //         if($dist <= 5){
    //             $distance[] =   $dist;
    //         }
    //     }
    //     if($distance){
    //         return $this->result_ok($distance);
    //     }else{
    //         return $this->result_fail('No nearby Driver.');
    //     }
    // }
    public function GetDrivingDistance(Request $request)
    {
        $auth_user = User::find(auth('api')->user()->id);
        $lat1 = $auth_user->lat;
        $long1 = $auth_user->long;
        $users = User::where('user_role', DRIVER)->get();
        foreach ($users as $user) {
            $lat2 = $user->lat;
            $long2 = $user->long;
            $theta = $long1 - $long2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            //in KMs
            $miles = $dist * 60 * 1.1515 * 1.609344;
            if ($miles <= 5) {
                $data['name'][]    =   $user->name;
                $data['lat'][]     =   $lat2;
                $data['long'][]    =   $long2;
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
        foreach ($data['name'] as $key => $name) {
            $details[$key]['driver_name'] = $name;
        }
        foreach ($data['lat'] as $key1 => $lat) {
            $details[$key1]['lat'] = $lat;
        }
        foreach ($data['long'] as $key2 => $long) {
            $details[$key2]['long'] = $long;
        }
        if (!empty($details)) {
            return $this->result_ok('Nearby Drivers', $details);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function getLocation(Request $request)
    {
        $lat = $request->lat;
        $long = $request->long;
        $geolocation = $lat . ',' . $long;
        $request = 'https://maps.googleapis.com/maps/api/geocode/json?key=' . env('PLACES_API') . '&latlng=' . $geolocation . '&sensor=false';
        $file_contents = file_get_contents($request);
        $json_decode = json_decode($file_contents);
        if (isset($json_decode->results[0])) {
            $response = array();
            foreach ($json_decode->results[0]->address_components as $addressComponet) {
                if (in_array('political', $addressComponet->types)) {
                    $responses[] = $addressComponet->long_name;
                }
            }

            if (isset($responses[0])) {
                $first  =  $responses[0];
            } else {
                $first  = 'null';
            }
            if (isset($responses[1])) {
                $second =  $responses[1];
            } else {
                $second = 'null';
            }
            if (isset($responses[2])) {
                $third  =  $responses[2];
            } else {
                $third  = 'null';
            }
            if (isset($responses[3])) {
                $fourth =  $responses[3];
            } else {
                $fourth = 'null';
            }
            if (isset($responses[4])) {
                $fifth  =  $responses[4];
            } else {
                $fifth  = 'null';
            }
            if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null') {
                $response['adress'] = $first;
                $response['city'] = $second;
                $response['state'] = $fourth;
                $response['country'] = $fifth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null') {
                $response['adress'] = $first;
                $response['city'] = $second;
                $response['state'] = $third;
                $response['country'] = $fourth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null') {
                $response['city'] = $first;
                $response['state'] = $second;
                $response['country'] = $third;
            } else if ($first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                $response['state'] = $first;
                $response['country'] = $second;
            } else if ($first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                $response['country'] = $first;
            }
            $response['complete_address']   =   $json_decode->results[0]->formatted_address;
            return $this->result_ok($response);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function createJob(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'source'    => 'required',
            'destination'   => 'required',
            'lat'           =>  'required',
            'long'          =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
        }
    }
}
