<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\User;
use App\Models\Payout;
use App\Models\Commission;
use App\Models\DriverTotal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $driver = DriverTotal::with('user')->get();
        return view('admin.payout.index', compact('driver'));
    }

    public function stripe(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'destination'      =>  'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $driver = DriverTotal::with('user')->first();
            $commis = Commission::first();
            $totalcommission = $driver['amount'] * $commis['commission'] / 100;
            $totalDriverPay['amounts'] = $driver['amount'] - $totalcommission;
            //  return $totalDriverPay['amounts'];
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRETKEY'));
            // dd($stripe);

            $data = $stripe->payouts->create([
                'amount' => round($totalDriverPay['amounts']),
                'currency' => 'usd',
                "description" => "Payment",
                'destination' => $request->BankId,
            ]);

            $totalPayout = Payout::create([
                "data" => $data
            ]);

            // return response()->json([
            //     'data' => $data
            // ]);
            return redirect('admin/payout')->with("Data succesfully Payment");
        }
    }
}
