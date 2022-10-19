<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Validator, Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Commission;

class CommissionController extends Controller
{

    public function index()
    {
        $commission = Commission::first();
        return view('admin.commission.index', compact('commission'));
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
            $commission = Commission::first();
            if(empty($commission)){
                $com = [
                    'commission'  =>  $data['name'],
                ];
                $s = Commission::create($com);
            }else{
                $s = $commission->update([
                    'commission'  =>  $data['name'],
                ]);
            }
            if ($s) {
                return redirect()->to('admin/commission')->with('success', 'Commmision Updated Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }
}
