<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\User;
use App\Models\RepPosition;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->section  = 'User';
        $this->view     = 'users';
    }

    public function index()
    {
        $section = $this->section;
        $users = User::where('user_role', USER)->get();
        return view('admin.' . $this->view . '.index', compact('users', 'section'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            return view('admin.' . $this->view . '.edit', compact('user'));
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'     =>  'required',
            'uname'         =>  'required|unique:users,username,' . $id,
            'email'         =>  'required|email|unique:users,email,' . $id,
            'phone_number'  =>  'required|numeric|unique:users,phone_number,' . $id,
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $userdata = User::find($id);
            if ($userdata) {
                $userdata->update([
                    'name'              =>  $data['name'],
                    'username'          =>  strtolower(trim($data['uname'])),
                    'email'             =>  strtolower(trim($data['email'])),
                    'phone_number'      =>  $data['phone_number'],
                ]);

                if ($request->has('image')) {
                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->storeAs('public/images', $imageName);
                    $userdata->update(['image'  =>  $imageName]);
                }
                return redirect()->to('admin/users')->with('success', 'User Details Updated Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong');
            }
        }
    }

    public function view($id)
    {
        $user = User::find($id);
        if ($user) {
            return view('admin.' . $this->view . '.view', compact('user'));
        }
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'User Deleted Successfully.');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong.');
        }
    }

    public function changeStatus($id, $status)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'is_active' =>  $status
            ]);
            return back()->with('success', 'Driver Status Updated Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }

}
