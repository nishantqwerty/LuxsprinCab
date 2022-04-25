<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\ApiController;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends ApiController
{

    public function onlineOffline($status)
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user->update([
                'is_online' =>  $status
            ]);
            return $this->result_ok('Status Updated Successfully.');
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function bankAccount(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'bank_name'                 =>  'required',
            'account_number'            =>  'required|unique:bank_accounts,account_number',
            'confirm_account_number'    =>  'required|same:account_number',
            'ifsc_code'                 =>  'required',
            'beneficiary_name'          =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        }else{
            $details = array(
                'user_id'   =>  auth('api')->user()->id,
                'bank_name' =>  $data['bank_name'],
                'account_number' =>  $data['account_number'],
                'ifsc_code' =>  $data['ifsc_code'],
                'beneficiary_name' =>  $data['beneficiary_name'],
            );
            if(BankAccount::create($details)){
                return $this->result_ok('Bank Account Added Successfully.');
            }else{
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
