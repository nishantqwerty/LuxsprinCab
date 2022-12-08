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
        // $transactions = Transaction::with('user')->get();
        $transactions = Transaction::with('user')->paginate(RECORDS_PER_PAGE);
        return view('admin.transaction.index', compact('transactions'));
    }

    public function refund(){
        $id = $_GET['id'];
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRETKEY'));
         $charge =  $stripe->refunds->create([
            'charge' => $id,
          ]);
        if($charge){
            $user = Transaction::where('charge_id', $id)->first();
            if ($user) {
                $refund_transaction = [
                    'user_id' => $user->user_id,
                    'booking_id' => $user->booking_id,
                    'driver_id' => $user->driver_id,
                    'payment_id' => $charge['id'],
                    'amount'    =>  $charge['amount'],
                    'customer_id' => $user->customer_id,
                    'payment_method'    =>  $charge['object'],
                    'payment_mode'    =>  NULL,
                    'status'    =>  $charge['status'],
                    'receipt_url'   => NULL,
                    'is_refunded'   =>  1
                ];
                Transaction::create($refund_transaction);
            }
            return redirect()->back()->with('success','Amount Refunded Successfully.');
        }else{
            return redirect()->back()->with('error','Something Went Wrong.');
        }
    }
}
