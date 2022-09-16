<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Models\Otp;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use App\Models\Booking;
use App\Models\CancelReason;
use App\Models\Panic;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Serverless\V1\Service\FunctionInstance;

class ProfileController extends ApiController
{
    public function allUsers()
    {
        $users = User::where('user_role', USER)->get();
        if ($users) {
            return $this->result_ok($users);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function dashboard()
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            return $this->result_ok('User Information', Auth::user());
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function profile()
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user['image']  =   asset('storage/images/' . $user->image);
            return $this->result_ok('User', $user);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'          =>  'required',
            'email'         =>  'required|email|unique:users,email,' . auth('api')->user()->id,
            'phone_number'  =>  'required|numeric|unique:users,phone_number,' . auth('api')->user()->id,
            'username'      =>  'required|unique:users,username,' . auth('api')->user()->id
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
                if ($request->has('image')) {
                    $filename = time() . '.' . $request->image->extension();
                    $request->image->storeAs('public/images', $filename);
                    $user->update([
                        'image' =>  $filename,
                    ]);
                }
                if ($data['phone_number'] == $user->phone_number) {
                    $user->update([
                        'name'  =>  $data['name'],
                        'email' =>  $data['email'],
                        'phone_number'  =>  $data['phone_number'],
                        'username'  =>  $data['username'],
                    ]);
                    return $this->result_message('User Information Updated Successfully.');
                } else {
                    $findotp = Otp::where('phone_number', $data['phone_number'])->first();
                    if (isset($data['otp'])) {
                        if ($findotp->otp == $data['otp']) {
                            $user->update([
                                'name'  =>  $data['name'],
                                'email' =>  $data['email'],
                                'phone_number'  =>  $data['phone_number'],
                                'username'  =>  $data['username'],
                                'country_code'  =>  $data['country_code'],
                            ]);
                            $findotp->delete();
                            return $this->result_message('User Information Updated Successfully.');
                        } else {
                            return $this->result_message('Please check your otp.');
                        }
                    } else {

                        $otp = rand(1000, 9999);
                        if (mb_substr($data['phone_number'], 0, 1) == 1) {
                            $this->us_otp($data['phone_number'], $otp);
                        } else {
                            $this->otp($data['phone_number'], $otp);
                        }
                        // $this->otp($data['phone_number'], $otp);
                        if ($findotp) {
                            $findotp->update([
                                'otp'   =>  $otp,
                            ]);
                        } else {
                            Otp::create([
                                'phone_number'  =>  $data['phone_number'],
                                'otp'           =>  $otp
                            ]);
                        }
                        return $this->result_message('Please enter otp to verify your number.');
                    }
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function changePassword(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'old_password'  =>  'required',
            'new_password'  =>  'required|min:6',
            'conf_password' =>  'required|same:new_password'
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
                $check_passeword    =   Hash::check($data['old_password'], $user->password);
                if ($check_passeword) {
                    $user->update([
                        'password'  =>  Hash::make($data['new_password']),
                    ]);
                    return $this->result_message('Password Changed Successfully.');
                } else {
                    return $this->result_fail('Please check your old passsword.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

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

    function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

        return array('distance' => $dist, 'time' => $time);
    }

    function getLocation($lat, $long)
    {
        $geolocation = $lat . ',' . $long;
        $request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $geolocation . '&sensor=false';
        $file_contents = file_get_contents($request);
        $json_decode = json_decode($file_contents);
        return $json_decode;
        if (isset($json_decode->results[0])) {
            $response = array();
            foreach ($json_decode->results[0]->address_components as $addressComponet) {
                if (in_array('political', $addressComponet->types)) {
                    $response[] = $addressComponet->long_name;
                }
            }

            if (isset($response[0])) {
                $first  =  $response[0];
            } else {
                $first  = 'null';
            }
            if (isset($response[1])) {
                $second =  $response[1];
            } else {
                $second = 'null';
            }
            if (isset($response[2])) {
                $third  =  $response[2];
            } else {
                $third  = 'null';
            }
            if (isset($response[3])) {
                $fourth =  $response[3];
            } else {
                $fourth = 'null';
            }
            if (isset($response[4])) {
                $fifth  =  $response[4];
            } else {
                $fifth  = 'null';
            }

            if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth != 'null') {
                echo "<br/>Address:: " . $first;
                echo "<br/>City:: " . $second;
                echo "<br/>State:: " . $fourth;
                echo "<br/>Country:: " . $fifth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth != 'null' && $fifth == 'null') {
                echo "<br/>Address:: " . $first;
                echo "<br/>City:: " . $second;
                echo "<br/>State:: " . $third;
                echo "<br/>Country:: " . $fourth;
            } else if ($first != 'null' && $second != 'null' && $third != 'null' && $fourth == 'null' && $fifth == 'null') {
                echo "<br/>City:: " . $first;
                echo "<br/>State:: " . $second;
                echo "<br/>Country:: " . $third;
            } else if ($first != 'null' && $second != 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                echo "<br/>State:: " . $first;
                echo "<br/>Country:: " . $second;
            } else if ($first != 'null' && $second == 'null' && $third == 'null' && $fourth == 'null' && $fifth == 'null') {
                echo "<br/>Country:: " . $first;
            }
        }
    }

    public function getChat()
    {
        $chats = UserChat::where('chat_room_id', auth('api')->user()->id)->with('user')->get();
        if ($chats) {
            return $this->result_ok('User Chat', $chats);
        } else {
            return $this->result_("Something Went Wrong.");
        }
    }

    public function faqs()
    {
        $faqs = Faq::get();
        if ($faqs) {
            return $this->result_ok('faqs', $faqs);
        } else {
            return $this->result_("Something Went Wrong.");
        }
    }

    public function sendMessage(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'message'     =>  'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {

            $UserChat  = [

                'message' => $data['message'],
                'chat_room_id' => auth('api')->user()->id,
                'user_id' => auth('api')->user()->id,
                'user_role' => USER,
            ];

            $chat = UserChat::create($UserChat);
            if ($chat) {
                $update_chat = Chat::where('chat_room_id', auth('api')->user()->id)->first();
                if ($update_chat) {
                    $update_chat->update([
                        'message'   => $data['message']
                    ]);
                } else {
                    $chat_data = [
                        'message' => $data['message'],
                        'chat_room_id' => auth('api')->user()->id,
                        'user_id' => auth('api')->user()->id,
                    ];
                    $chat = Chat::create($chat_data);
                }
                return $this->result_message("Message Added");
            } else {
                return $this->result_fail("Something Went Wrong.");
            }
        }
    }

    public function submitRating(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required',
            'rating'        =>  'required',
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
                $rating_data = [
                    'user_id'       =>  auth('api')->user()->id,
                    'booking_id'    =>  $data['booking_id'],
                    'driver_id'     =>  $booking->driver_id,
                    'rating'        =>  $data['rating'],
                    'review'        =>  isset($data['review']) ? $data['review'] : NULL
                ];
                $rating = Rating::create($rating_data);
                if ($rating) {
                    return $this->result_message('Rating Submitted Successfully.');
                } else {
                    return $this->result_fail('Something Went Wrong.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function showAllRating()
    {
        $rating = Rating::where('user_id', auth('api')->user()->id)->get();
        if ($rating) {
            return $this->result_message('Ratings', $rating);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function showBookingRating($id)
    {
        $rating = Rating::where('user_id', auth('api')->user()->id)->where('booking_id', $id)->first();
        if ($rating) {
            return $this->result_message('Ratings', $rating);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function cancelReason()
    {
        $reasons = CancelReason::where('user_role', USER)->get();
        if ($reasons) {
            return $this->result_ok('Cancellation Reasons', $reasons);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function panic(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'lat'        =>  'required',
            'long'        =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user = [
                'user_id' => auth('api')->user()->id,
                'lat'       =>  $data['lat'],
                'long'       =>  $data['long'],
            ];
            $panic = Panic::create($user);
            if ($panic) {
                return $this->result_ok('Emergency Reported');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function transaction(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'trans_id'        =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRETKEY'));
            $charge =  $stripe->paymentIntents->retrieve(
                $data['trans_id'],
                []
            );
            $trans_data = [
                'user_id' => auth('api')->user()->id,
                'payment_id' => $charge['id'],
                'amount'    =>  $charge['amount'],
                'customer_id' => $charge['customer'],
                'payment_method'    =>  $charge['payment_method'],
                'status'    =>  $charge['status'],
                'receipt_url'   => $charge['charges']['data'][0]['receipt_url'],
                'is_refunded'   =>  0
            ];

            $transaction = Transaction::create($trans_data);
            if($transaction){
                return $this->result_message('Transaction Added.');
            }else{
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
