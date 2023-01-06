<?php

namespace App\Http\Controllers\Driver;

use Hash;
use App\Models\Faq;
use App\Models\Otp;
use App\Models\Chat;
use App\Models\User;
use App\Models\Panic;
use App\Models\Rating;
use App\Models\Booking;
use App\Models\UserChat;
use App\Models\CarDetail;
use App\Models\Transaction;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use App\Models\RatingMessage;
use App\Models\RejectDocument;
use App\Models\CancellationEarning;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class ProfileController extends ApiController
{
    public function updateProfile(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'          =>  'required',
            'email'         =>  'required|email|unique:users,email,' . auth('api')->user()->id,
            'phone_number'  =>  'required|numeric|unique:users,phone_number,' . auth('api')->user()->id,
            'username'      =>  'required|unique:users,username,' . auth('api')->user()->id,
            // 'address'       =>  'required'
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
                        'address' => $data['address'],
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
                                'address' => $data['address'],
                                'country_code' =>  $data['country_code'],
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
            $booking = Booking::where('id', $data['booking_id'])->where('driver_id', auth('api')->user()->id)->first();
            if ($booking) {
                $arr = $data['review'];
                    $res = implode(",", $arr);
                $rating_data = [
                    'user_id'       =>  $booking->user_id,
                    'booking_id'    =>  $data['booking_id'],
                    'driver_id'     =>  auth('api')->user()->id,
                    'rating'        =>  $data['rating'],
                    'review'        =>  $res
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
        $rating = Rating::where('driver_id', auth('api')->user()->id)->orderBy('created_at', 'DESC')->get();
        if ($rating) {
            return $this->result_message('Ratings', $rating);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function RatingMessages()
    {
        $reasons = RatingMessage::select('id','messages','created_at','updated_at')->where('role',DRIVER)->get();
        if ($reasons) {
            return $this->result_ok('Rating Messages', $reasons);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function showBookingRating($id)
    {
        $rating = Rating::where('driver_id', auth('api')->user()->id)->where('booking_id', $id)->first();
        if ($rating) {
            return $this->result_message('Ratings', $rating);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function getProfile()
    {
        $user = User::where('id', auth('api')->user()->id)->with('carDetail')->first();
        if ($user) {
            if(!empty($user['image'])){
                $user['image']  =   asset("storage/images/$user->image");
            }else{
                $user['image']  =   asset("dist/img/no_image.png");
            }
            return $this->result_ok('User Detail', $user);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function getChat()
    {
        $chats = UserChat::where('chat_room_id', auth('api')->user()->id)->get();
        if ($chats) {
            return $this->result_ok('Chats', $chats);
        } else {
            return $this->result_error("Something Went Wrong.");
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
                'user_role' => DRIVER,
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
                return $this->result_ok("Message Added");
            } else {
                return $this->result_fail("Something Went Wrong.");
            }
        }
    }

    public function getStatus()
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $data = [];
            $data['status'] = $user->is_validated;
            $data['description'] = "";
            if ($user->is_validated == DRIVER_REJECTED) {
                $reject = RejectDocument::where('user_id', auth('api')->user()->id)->first();
                if ($reject) {
                    $data['description'] = strip_tags($reject->description);
                }
            }
            return $this->result_ok('Userstatus', $data);
        } else {
            return $this->result_fail('Something went wrong');
        }
    }

    public function updateLocation($lat, $long)
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user->update([
                'lat'   => $lat,
                'long'  =>  $long,
            ]);
            return $this->result_message('Location Updated Successfully.');
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function acceptReject(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required',
            'status'        =>  'required'
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
                if ($booking->driver_id == 0) {
                    if ($data['status'] == ACCEPT_BOOKING) {
                        $booking->update([
                            'driver_id' => auth('api')->user()->id,
                        ]);
                        $user = User::find($booking->user_id);
                        $driver = User::find(auth('api')->user()->id);
                       
                        $msgdata = [
                            'id'            =>  $driver->id,
                            'device_token'  =>  $user->device_token,
                            'message'       =>  'Booking Accepted',
                            'driver_image'  =>  !empty($driver->image)   ?    asset('storage/images/' . $driver->image)  :   'no_image',
                            'driver_name'   =>  !empty($driver->name)   ?   $driver->name  :   'NULL',
                            'booking_id'    =>  $booking->id,
                            'fare'          =>  $booking->fare
                        ];
                        $sen = $this->sendAcceptNotification($msgdata);
                        return $this->result_ok('Booking has been Accepted.', ['user_details' => $user, 'fare' => $msgdata['fare']]);
                    } else {
                        return $this->result_message('Booking has been rejected succesfully.');
                    }
                } else {
                    return $this->result_fail('Booking already accepted by another driver.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function acceptRejectSharing(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'booking_id'    =>  'required',
            'status'        =>  'required'
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
                if ($booking->driver_id == 0) {
                    if ($data['status'] == ACCEPT_BOOKING) {
                        $booking->update([
                            'driver_id' => auth('api')->user()->id,
                        ]);
                        $car_detail = CarDetail::where('user_id',auth('api')->user()->id)->first();
                        $car_detail->update([
                            'available_seats'   =>  $car_detail->available_seats - $booking->seats
                        ]);

                        $user = User::find($booking->user_id);
                        $driver = User::find(auth('api')->user()->id);
                        $msgdata = [
                            'id'            =>  $driver->id,
                            'device_token'  =>  $user->device_token,
                            'message'       =>  'Booking Accepted',
                            'driver_image'  =>  !empty($driver->image)   ?   asset('storage/images/' . $driver->image)  :   'no_image',
                            'driver_name'   =>  !empty($driver->name)   ?   $driver->name  :   'NULL',
                            'booking_id'    =>  $booking->id,
                            'fare'          =>  $booking->fare
                        ];
                        $sen = $this->sendAcceptNotification($msgdata);
                        return $this->result_ok('Booking has been Accepted.', ['user_details' => $user, 'fare' => $msgdata['fare']]);
                    } else {
                        return $this->result_message('Booking has been rejected succesfully.');
                    }
                } else {
                    return $this->result_fail('Booking already accepted by another driver.');
                }
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
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

    public function cancelReason()
    {
        $reasons = CancelReason::where('user_role', DRIVER)->get();
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
            'booking_id'        =>  'required',
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
                'booking_id' => $data['booking_id'],
            ];
            $panic = Panic::create($user);
            if ($panic) {
                return $this->result_message('Emergency Reported');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function myEarning(Request $request)
    { 
        // $user=auth('api')->user()->id;       
        $user = Transaction::where('driver_id', auth('api')->user()->id)->orderBy('created_at', 'DESC')->get();
        if (isset($request->date_from) && isset($request->date_to)) {
            $date_from = date('Y-m-d', strtotime($request->date_from));
            $date_to = date('Y-m-d', strtotime($request->date_to));
            
            $user = Transaction::where('driver_id', auth('api')->user()->id)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to)->get();
        }
        if ($user) {
            $usertotal = $user->sum('amount');
            return $this->result_ok('Earnings',['details'=>$user,'total_amount' => $usertotal]);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function bookingEarning($id)
    {
        $user = Booking::where('id', $id)->where('driver_id', auth('api')->user()->id)->with('transaction')->first();
        if ($user) {
            return $this->result_ok('Booking Payment',$user);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function bookingReport(Request $request)
    {
        $user = Booking::where('driver_id', auth('api')->user()->id)->orderBy('created_at', 'DESC')->get();
        if ($user) {
            if (isset($request->date_from) && isset($request->date_to)) {
                $date_from = date('Y-m-d', strtotime($request->date_from));
                $date_to = date('Y-m-d', strtotime($request->date_to));

                $successful_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', 0)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);
                $cancelled_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', BOOKING_CANCEL)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);
                $pending_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_completed', RIDE_ONGOING)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);
            } else {
                $successful_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', 0);
                $cancelled_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', BOOKING_CANCEL);
                $pending_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_completed', RIDE_ONGOING);
            }
            $data['successfull_booking'] = $successful_booking->get()->count();
            $data['cancelled_booking'] = $cancelled_booking->get()->count();
            $data['pending_booking'] = $pending_booking->get()->count();
            $data['total'] = $data['pending_booking'] + $data['successfull_booking'] + $data['cancelled_booking'];
            return $this->result_ok('Report',$data);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    } 

    public function earningReport(Request $request)
    {
        $user = Transaction::where('driver_id', auth('api')->user()->id)->orderBy('created_at', 'DESC')->get();
        if ($user) {
            if (isset($request->date_from) && isset($request->date_to)) {
                $date_from = date('Y-m-d', strtotime($request->date_from));
                $date_to = date('Y-m-d', strtotime($request->date_to));

                $successful_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 1)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);;
                $cancelled_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 0)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);;
                $cancellation_charges = CancellationEarning::where('driver_id', auth('api')->user()->id)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);;
            } else {
                $successful_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 1);
                $cancelled_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 0);
                $cancellation_charges = CancellationEarning::where('driver_id', auth('api')->user()->id);
            }
            $data['payment_done'] = $successful_booking->get()->sum('amount');
            $data['payment_on_hold'] = $cancelled_booking->get()->sum('amount');
            $data['earning_from_cancellation'] = $cancellation_charges->get()->sum('amount');
            $data['total'] = $data['payment_done'] + $data['payment_on_hold'] + $data['earning_from_cancellation'];
            return $this->result_ok('Earning Report',$data);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function tokenUpdate(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'device_type'        =>  'required',
            'device_token'        =>  'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $token = User::find(auth('api')->user()->id);

            if ($token) {
                $token->update([
                    'device_type' => $request['device_type'],
                    'device_token' => $request['device_token'],
                ]);
                return $this->result_message('Token Updated');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
