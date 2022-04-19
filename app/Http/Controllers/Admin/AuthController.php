<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator, Hash, Mail, URL, Str;

class AuthController extends Controller
{
    public function login()
    {
        return redirect('admin/login');
    }

    public function register()
    {
        return view('admin.auth.register');
    }

    public function login_post()
    {
        return View('admin.auth.login');
    }

    public function forgetPassword()
    {
        return View('admin.auth.forgot-password');
    }

    public function login_attempt(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email'     =>  'required',
            'password'  =>  'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $userdata = array(
                'email'  =>  $data['email'],
                'password'  =>  $data['password'],
                'user_role'  =>  SUPER_ADMIN
            );
            if (Auth::attempt($userdata)) {
                return redirect()->to('/admin/dashboard')
                    ->with('success', 'Admin logged in successfully.');
            } else {
                return redirect()->back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function register_attempt(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'              =>  'required',
            'email'             =>  'required',
            'password'          =>  'required',
            'confirm-password'  =>  'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $user = new User;
            $user->name =   $data['name'];
            $user->email    =   $data['email'];
            $user->password =   Hash::make($data['password']);
            $user->is_admin =   SUPER_ADMIN;
            if ($user->save()) {
                return redirect()->to('admin/login')
                    ->with('success', 'User created successfully.');
            } else {
                return redirect()->back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function logout()
    {

        Auth::logout();
        return redirect('/admin/login');
    }

    public function sendforgetPassword(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'email' =>  'required|email'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $user = User::whereEmail($data['email'])->first();
            if ($user) {
                $user->update([
                    'is_password_reset'    =>  PASSWORD_RESET_REQUEST,
                    'forgot_password'      =>  sha1($user->email) . Str::random(40),
                ]);
                $url = URL::to("/admin/reset-password/$user->forgot_password");
                Mail::to($user->email)->send(new ForgotPassword($user->email, $url));
                return redirect()->to('/admin/login')->with('success', 'An Email has been sent to your registered email address.');
            } else {
                return redirect()->to('/admin/forget-password')->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function resetPassword($string)
    {
        $string = $string;
        $user = User::where('forgot_password', $string)->where('is_password_reset', PASSWORD_RESET_REQUEST)->where('user_role',SUPER_ADMIN)->first();
        if ($user) {
            return view('admin.auth.reset-password',compact('string'));
        } else {
            return redirect()->to('/admin/login')->with('error', 'Something Went Wrong.');
        }
    }

    public function saveNewPassword(Request $request,$string)
    {
        $data = $request->all();
        $validator = Validator::make($data,[
            'password'          =>  'required|min:6',
            'confirm_password'  =>  'required|same:password'
        ]);
        if($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            $user = User::where('forgot_password',$string)->where('is_password_reset',PASSWORD_RESET_REQUEST)->first();
            if($user){
                $user->update([
                    'forgot_password'   =>  NULL,
                    'is_password_reset' =>  PASSWORD_CHANGED,
                    'password'          =>  Hash::make($data['password'])
                ]);
                return redirect()->to('/admin/login')->with('success','Password Changed Successfully.');
            }else{
                return redirect()->back()->with('error','Something Went Wrong.');
            }
        }
    }
}
