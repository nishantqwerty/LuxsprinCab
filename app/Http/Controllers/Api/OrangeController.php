<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Ibracilinks\OrangeMoney\OrangeMoney;
use Illuminate\Support\Facades\Validator;

class OrangeController extends ApiController
{

    public function payment(Request $request)
    {
        $payment = new OrangeMoney();

        $data = [
            "merchant_key" => env('OM_MERCHANT_KEY'),
            "currency" => "XAF",
            "order_id" => "" . time() . "",
            "amount" => $request->amount,
            "return_url" => env('OM_RETURN_URL'),
            "cancel_url" => env('OM_CANCEL_URL'),
            "notif_url" => env('OM_NOTIF_URL'),
            "lang" => "fr",
            // "reference" => "Your Website"
        ];

        // return $options;
        $success = $payment->webPayment($data);
        if ($success) {
            $success['order_id'] = $data['order_id'];
            return $this->result_ok('Payment',$success);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function checkStatus(Request $request)
    {
        $payment = new OrangeMoney();
        $data = $request->all();
        $validator = Validator::make(
        $data, [
            "order_id" => 'required',
            "amount" => 'required',
            "pay_token" => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $orderId = $data['order_id'];
            $amount = $data['amount'];
            $pay_token = $data['pay_token'];

            // return $options;
            $success = $payment->checkTransactionStatus($orderId, $amount, $pay_token);
            if ($success) {
                return $this->result_ok('Payment Status',$success);
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
