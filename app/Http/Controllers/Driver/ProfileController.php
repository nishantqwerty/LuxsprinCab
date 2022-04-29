<?php

namespace App\Http\Controllers\Driver;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
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
                    if (isset($data['otp'])) {
                        $findotp = Otp::where('phone_number', $data['phone_number'])->first();
                        if ($findotp->otp == $data['otp']) {
                            $user->update([
                                'name'  =>  $data['name'],
                                'email' =>  $data['email'],
                                'phone_number'  =>  $data['phone_number'],
                                'username'  =>  $data['username'],
                            ]);
                            $findotp->delete();
                            return $this->result_message('User Information Updated Successfully.');
                        } else {
                            return $this->result_message('Please check your otp.');
                        }
                    } else {
                        $otp = rand(1000, 9999);
                        $this->otp($data['phone_number'], $otp);
                        $update = Otp::create([
                            'phone_number'  =>  $data['phone_number'],
                            'otp'           =>  $otp
                        ]);
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
            return $this->result_ok('User Detail', $user);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
