<?php

namespace App\Http\Controllers\Api;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ForgotUsername;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class LoginController extends ApiController
{

    public function login(Request $request)
    {
        $data = $request->all();
        $login = $request->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $request->merge([$fieldType => $login]);
        $validator = Validator::make($data, [
            'login'  => 'required',
            'password'  => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user = User::where($fieldType, $login)->where('user_role', USER)->first();
            if ($user) {
                if ($user->is_active == USER_ACTIVE) {
                    if (Auth::attempt($request->only($fieldType, 'password'))) {
                        $token = $user->createToken('Auth Token')->accessToken;
                        return $this->result_ok('User Logged In.', ['token' => $token, 'user' => Auth::user()]);
                    } else {
                        return $this->result_fail("Please check your email or password");
                    }
                } else {
                    return $this->result_fail("Your account is inactive, please contact administrator.");
                }
            } else {
                return $this->result_fail('Account does not exists.');
            }
        }
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator  =   Validator::make($data, [
            'name'  =>  'required',
            'username'      =>  'required|unique:users,username',
            'email'         =>  'required|email|unique:users,email',
            'phone_number'  =>  'required|numeric|unique:users,phone_number',
            'password'      =>  'required|min:6',
            'confirm_password'  =>  'required|same:password',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $userdata   =   array(
                'name'  =>  $data['name'],
                'username'  =>  strtolower(trim($data['username'])),
                'email'     =>  $data['email'],
                'phone_number'  =>  $data['phone_number'],
                'password'  =>  Hash::make($data['password']),
                'user_role' =>  USER,
                'is_active' =>  USER_ACTIVE,
            );
            $otp = rand(1000, 9999);
            $userotp = Otp::where('phone_number', $data['phone_number'])->first();
            if (!empty($userotp)) {
                if (isset($data['otp'])) {
                    if ($data['otp'] == $userotp->otp) {
                        $user = User::create($userdata);
                        if ($user) {
                            $userotp->delete();
                            return $this->result_message('User Registered Successfully.');
                        } else {
                            return $this->result_fail('Something Went Wrong.');
                        }
                    } else {
                        return $this->result_fail('Please check your otp again');
                    }
                }
            } else {
                $this->otp($data['phone_number'], $otp);
                $otp = Otp::create([
                    'phone_number'  =>  $data['phone_number'],
                    'otp'           =>  $otp
                ]);
            }
            return $this->result_message('Please enter otp to verify your account.');
        }
    }

    public function forgotUsername(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' =>  'required|email',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                Mail::to($user->email)->send(new ForgotUsername($user->email, $user->username));
                return $this->result_message('An email has been sent.Please check your email.');
            } else {
                return $this->result_fail('Please check your email address.');
            }
        }
    }

    public function sendOtp(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'phone_number'  =>  'required|numeric',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user = User::where('phone_number', $data['phone_number'])->first();
            if ($user) {
                $otp = rand(1000, 9999);
                $response = $this->otp($data['phone_number'], $otp);
                $user->update([
                    'otp'   =>  $otp
                ]);
                return $response;
                // return $this->result__message('Otp has been sent to your registered phone number.');
            } else {
                return $this->result_fail('This phone number does not exists with us.');
            }
        }
    }

    public function resendOtp(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'phone_number'  =>  'required|numeric',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $otp = rand(1000, 9999);
            $number = Otp::where('phone_number', $data['phone_number'])->first();
            if ($number) {
                $this->otp($data['phone_number'], $otp);
                $number->update([
                    'otp'   =>  $otp,
                ]);
                return $this->result_message('Otp Resend Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->all();
        $validator  =   Validator::make($data, [
            'otp'   =>  'required',
            'phone_number'  =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $user   =   User::where('phone_number', $data['phone_number'])->first();
            if ($user) {
                if ($data['otp'] == $user->otp) {
                    $user->update([
                        'otp'   =>  0
                    ]);
                    return $this->result_message('Otp Verified.');
                } else {
                    return $this->result_fail('Please check your otp again.');
                }
            } else {
                return $this->result_fail('Please check phone number.');
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $data = $request->all();
        $validator  =   Validator::make($data, [
            'phone_number'      =>  'required|numeric',
            'password'          =>  'required|min:6',
            'confirm_password'  =>  'required|same:password'
        ]);
        if ($validator->fails()) {
            $errors =   $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error)
                    return $this->result_message($error);
            }
        } else {
            $user = User::where('phone_number', $data['phone_number'])->first();
            if ($user) {
                $user->update([
                    'password'  =>  Hash::make($data['password'])
                ]);
                return $this->result_message("Password Updates Successfully.");
            } else {
                return $this->result_fail('No Account existes with this number.');
            }
        }
    }

    public function logout()
    {
        $user = auth('api')->user();
        if ($user) {
            $user->token()->revoke();
            return $this->result_message("User logged Out.");
        } else {
            return $this->result_fail("Something Went Wrong.");
        }
    }

    public function deleteAccount()
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user->delete();
            return $this->result_ok('Account Deleted Successfully.');
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
