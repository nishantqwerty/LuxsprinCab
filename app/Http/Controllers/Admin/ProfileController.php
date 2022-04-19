<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator, Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->section  = 'Profile';
        $this->view     = 'profile';
    }


    public function index()
    {
        return view('admin.' . $this->view . '.profile');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'     =>  'required',
            'uname'         =>  'required|unique:users,username,' . Auth::user()->id,
            'email'         =>  'required|email|unique:users,email,' . Auth::user()->id,
            'phone_number'  =>  'required|numeric|unique:users,phone_number,' . Auth::user()->id,
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        } else {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $userdata = array(
                    'name'          =>  $data['name'],
                    'username'      =>   strtolower(trim($data['uname'])),
                    'email'         =>  strtolower(trim($data['email'])),
                    'phone_number'  =>  $data['phone_number'],
                );

                if ($request->has('image')) {
                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->storeAs('public/images', $imageName);

                    $userdata['image']  =  $imageName;
                }

                $user->update($userdata);
                return redirect()->back()->with('success', 'Profile Updated Successfully.');
            } else {
                return redirect()->back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function changePassword()
    {
        return view("admin.$this->view.change-password");
    }

    public function savePassword(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'old_pass'      =>  'required',
            'password'      =>  'required|min:6',
            'confirm_pass'  =>  'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $user = User::find(Auth::user()->id);
            if ($user) {
                $pass_check =   Hash::check($data['old_pass'], $user->password);
                if ($pass_check) {
                    $user->update([
                        'password'  =>  Hash::make($data['password']),
                    ]);
                    return redirect()->to('admin/profile')->with('success', 'Password Changed Successfuly.');
                } else {
                    return back()->with('error', 'Please Check your old password');
                }
            }
        }
    }

    public function deleteImage($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'image' =>  NULL
            ]);
            return back()->with('success', 'Image Deleted Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }
}
