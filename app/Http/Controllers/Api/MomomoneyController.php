<?php

namespace App\Http\Controllers\Api;
use Bmatovu\MtnMomo\Products\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;



class MomomoneyController extends ApiController
{

    public function payment(Request $request)
  {
    $collection = new Collection();
    
       $data =  $request->all();
       $validator = Validator::make(
        $data, [
            "partyId" => 'required',
            "amount" => 'required',
            "payerMessage" => 'required',
        
        ]); 
        if ($validator->fails()) {
          
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        }  
            else {
                $partyId = $data["partyId"];
                $amount = $data['amount'];
                $payerMessage = $data['payerMessage'];
                    $success = $collection->requestToPay('transaction', $partyId, $amount, $payerMessage);
                    if ($success) { 
                        return $this->result_ok('Payment',$success);
                    } else {
                        return $this->result_fail('Something Went Wrong.');
                    }

                }   
        
    }
    public function checkStatus(Request $request)
    {
        $collection = new Collection();
        $data = $request->all();
        $validator = Validator::make(
        $data, [
           "momoTransactionId" => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $momoTransactionId = $data['momoTransactionId'];
            
          $success =   $collection->getTransactionStatus($momoTransactionId);
         
          if ($success['status']== 'SUCCESSFUL') {
            return $this->result_ok('Payment Status',$success);
        } else {
            return $this->result_fail( $success['message']);
        }
    }
}

}