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
    public function GetDrivingDistance(Request $request)
    {
        $lat1 = $request->lat;
        $long1 = $request->long;
        $users = User::where('user_role',DRIVER)->get();
        $response_a = [];
        foreach($users as $user){
            $lat2 = $user->lat;
            $long2 = $user->long;
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=" . env('PLACES_API') . "&origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a['data'] = json_decode($response, true);
            // $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            // $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
        }
        return $response_a;
        // return array('distance' => $dist, 'time' => $time);
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
        }else{
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
