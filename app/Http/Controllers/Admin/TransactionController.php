<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->get();
        return view('admin.transaction.index', compact('transactions'));
    }

    public function refund($id){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRETKEY'));
         $charge =  $stripe->refunds->create([
            'charge' => $id,
          ]);
        if($charge){
            return redirect()->back()->with('success','Amount Refunded Successfully.');
        }else{
            return redirect()->back()->with('error','Something Went Wrong.');
        }
    }
}
