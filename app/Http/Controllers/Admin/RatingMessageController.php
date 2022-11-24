<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CancelReason;
use App\Models\RatingMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingMessageController extends Controller
{
    public function index()
    {
        $reasons = RatingMessage::get();
        return view('admin.ratingmessage.index', compact('reasons'));
    }

    public function add()
    {
        return view('admin.ratingmessage.add');
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'reason'    =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            if (!empty($data)) {
                
                foreach ($data['reason'] as $reas) {
                    $cancel = new RatingMessage();
                    $cancel->messages    =   $reas;
                    $cancel->save();
                }
                return redirect()->to('admin/rating-messages')->with('success', 'Reason Added Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function edit($id)
    {
        $reason = RatingMessage::find($id);
        return view('admin.ratingmessage.edit', compact('reason'));
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
            $reason = RatingMessage::find($id);
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
