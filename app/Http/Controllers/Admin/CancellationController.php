<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CancellationController extends Controller
{
    public function index()
    {
        $reasons = CancelReason::get();
        return view('admin.cancel.index', compact('reasons'));
    }

    public function add()
    {
        return view('admin.cancel.add');
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'role'      =>  'required',
            'reason'    =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            if (!empty($data)) {
                foreach ($data['reason'] as $reas) {
                    $cancel = new CancelReason();
                    $cancel->user_role  = $data['role'];
                    $cancel->reasons    =   $reas;
                    $cancel->save();
                }
                return redirect()->to('admin/cancellation')->with('success', 'Reason Added Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function edit($id)
    {
        $reason = CancelReason::find($id);
        return view('admin.cancel.edit', compact('reason'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'role'      =>  'required',
            'reason'    =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $reason = CancelReason::find($id);
            if ($reason) {
                $reason->update([
                    'user_role' =>  $data['role'],
                    
                    'reasons'   =>  $data['reason']
                ]);
                return redirect()->to('admin/cancellation')->with('success', 'Reason Updated Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function delete($id)
    {
        $reason = CancelReason::find($id);
        if ($reason) {
            $reason->delete();
            return back()->with('success', 'Reason Deleted Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }
}
