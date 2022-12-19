<?php

namespace App\Http\Controllers\Driver;

use App\Models\DriverTotal;
use App\Models\User;
use App\Models\Payout;
use App\Models\Commission;
use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripePayment(Request $request)
    {  
        // dd($request->all());
        $driver = DriverTotal::with('user')->where('driver_id',auth('api')->user()->id)->first();
        $commis = Commission::first();
        $totalcommission = $driver['amount'] * $commis['commission'] / 100;
        $totalDriverPay['amounts'] = $driver['amount'] - $totalcommission;
        //  return $totalDriverPay['amounts'];
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRETKEY'));
        // dd($stripe);
        $data = $stripe->payouts->create([
            'amount' => round($totalDriverPay['amounts']),
            'currency' => 'usd',
            "description" => "Test payment",
            'destination' => $request->BankId,
        ]);
    //    dd($data)->toArray();
        Payout::create($data);
        return response()->json([
            'data'=>$data
        ]);
    }
    //
}
