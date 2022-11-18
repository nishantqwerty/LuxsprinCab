<?php

namespace App\Http\Controllers\Driver;

use Hash;
use App\Models\Faq;
use App\Models\Otp;
use App\Models\Chat;
use App\Models\User;
use App\Models\Panic;
use App\Models\Booking;
use App\Models\UserChat;
use App\Models\Transaction;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use App\Models\RejectDocument;
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

    public function getProfile()
    {
        $user = User::where('id', auth('api')->user()->id)->with('carDetail')->first();
        if ($user) {
            $user['image']  =   asset("storage/images/$user->image");
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
                            'driver_image'  =>  !empty($driver->image)   ?   $driver->image  :   'no_image',
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
                        $user = User::find($booking->user_id);
                        $driver = User::find(auth('api')->user()->id);
                        $msgdata = [
                            'id'            =>  $driver->id,
                            'device_token'  =>  $user->device_token,
                            'message'       =>  'Booking Accepted',
                            'driver_image'  =>  !empty($driver->image)   ?   $driver->image  :   'no_image',
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
                return $this->result_message('Emergency Reported');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function myEarning(Request $request)
    {
        $user = Transaction::where('driver_id', auth('api')->user()->id)->get();
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
        $user = Booking::where('driver_id', auth('api')->user()->id)->get();
        if ($user) {
            if (isset($request->date_from) && isset($request->date_to)) {
                $date_from = date('Y-m-d', strtotime($request->date_from));
                $date_to = date('Y-m-d', strtotime($request->date_to));

                $successful_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', 0)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);
                $cancelled_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', BOOKING_CANCEL)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);
            } else {
                $successful_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', 0);
                $cancelled_booking = Booking::where('driver_id', auth('api')->user()->id)->where('is_cancelled', BOOKING_CANCEL);
            }
            $data['successfull_booking'] = $successful_booking->get()->count();
            $data['cancelled_booking'] = $cancelled_booking->get()->count();
            return $this->result_ok('Report',$data);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    } 

    public function earningReport(Request $request)
    {
        $user = Transaction::where('driver_id', auth('api')->user()->id)->get();
        if ($user) {
            if (isset($request->date_from) && isset($request->date_to)) {
                $date_from = date('Y-m-d', strtotime($request->date_from));
                $date_to = date('Y-m-d', strtotime($request->date_to));

                $successful_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 1)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);;
                $cancelled_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 0)->whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to);;
            } else {
                $successful_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 1);
                $cancelled_booking = Transaction::where('driver_id', auth('api')->user()->id)->where('payment_status', 0);
            }
            $data['payment_done'] = $successful_booking->get()->sum('amount');
            $data['payment_on_hold'] = $cancelled_booking->get()->sum('amount');
            return $this->result_ok('Earning Report',$data);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
