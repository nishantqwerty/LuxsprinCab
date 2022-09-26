<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Ibracilinks\OrangeMoney\OrangeMoney;

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
            return $this->result_ok($success);
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }
}
