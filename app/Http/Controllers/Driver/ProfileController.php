<?php

namespace App\Http\Controllers\Driver;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\RejectDocument;
use Illuminate\Support\Facades\Validator;
use Hash;

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
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user['image']  =   asset("storage/images/$user->image");
            return $this->result_ok('User Detail', $user);
        } else {
            return $this->result_fail('Something Went Wrong.');
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
}
