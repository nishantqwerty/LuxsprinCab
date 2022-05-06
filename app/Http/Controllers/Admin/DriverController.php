<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CarDetail;
use App\Models\DriverDocument;
use App\Models\RejectDocument;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->section  = 'Driver';
        $this->view     = 'drivers';
    }

    public function index()
    {
        $section = $this->section;
        $users = User::where('user_role', DRIVER)->whereIn('is_validated',[DRIVER_APPROVED,RESUBMIT_DOCUMENT,DRIVER_UNDER_VERIFICATION])->get();
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
                return redirect()->to('admin/drivers')->with('success', 'Driver Details Updated Successfully.');
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
            return redirect()->back()->with('success', 'Driver Deleted Successfully.');
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

    public function acceptReject($id, $status)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'is_validated' =>  $status
            ]);
            return back()->with('success', 'Driver Status Updated Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }

    public function viewDocuments($id)
    {
        $car_detail = CarDetail::where('user_id', $id)->first();
        $document = DriverDocument::where('user_id', $id)->first();
        return view("admin.$this->view.documents", compact('car_detail', 'document'));
    }

    public function rejectDocuments($id, $status)
    {
        return view("admin.$this->view.reject", compact('id', 'status'));
    }

    public function saveRejectDocuments(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'message'   =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $document = RejectDocument::where('user_id', $id)->first();
            if (empty($document)) {
                $rejection = [
                    'user_id'   =>  $id,
                    'description'   =>  $data['message']
                ];
                $reject_document = RejectDocument::create($rejection);
            } else {
                $document->update([
                    'description'   =>  $data['message'],
                ]);
            }
            if ($document) {
                $user = User::find($id);
                if ($user) {
                    $user->update([
                        'is_validated'  =>  DRIVER_REJECTED
                    ]);
                }
                return redirect()->to('admin/drivers')->with('success', 'Driver Rejected Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }
}
