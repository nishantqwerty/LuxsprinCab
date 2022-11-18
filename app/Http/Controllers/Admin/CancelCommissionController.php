<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Validator, Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\CancelCommission;
use App\Models\Commission;

class CancelCommissionController extends Controller
{

    public function index()
    {
        $commission = CancelCommission::first();
        return view('admin.cancelcomm.index', compact('commission'));
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'      =>  'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $commission = CancelCommission::first();
            if(empty($commission)){
                $com = [
                    'commission'  =>  $data['name'],
                ];
                $s = CancelCommission::create($com);
            }else{
                $s = $commission->update([
                    'commission'  =>  $data['name'],
                ]);
            }
            if ($s) {
                return redirect()->to('admin/cancel-commission')->with('success', 'Commmision Updated Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }
}
