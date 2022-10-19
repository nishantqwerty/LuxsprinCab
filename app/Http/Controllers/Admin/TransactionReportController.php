<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionReportController extends Controller
{
    public function index()
    {
        $total = Transaction::get()->count();
        $stripe = Transaction::where('payment_mode', 'card')->get()->count();
        $orange = Transaction::where('payment_mode', 'orange')->get()->count();

        return view('admin.transaction-reports.index', compact('total', 'stripe', 'orange'));
    }


    public function date(Request $request)
    {
        if ((isset($request->start_date) && !isset($request->end_date)) || (!isset($request->start_date) && isset($request->end_date))) {
            return back()->with('error', 'Please Provide proper dates.');
        } else {
            if (isset($request->start_date) && isset($request->end_date)) {
                $start_date = date('Y-m-d', strtotime($request->start_date));
                $end_date = date('Y-m-d', strtotime($request->end_date));
                $ride_category = $request->ride_category;
                $total = Transaction::whereDate('created_at','>=', $start_date)->whereDate('created_at','<=',$end_date)->get()->count();
                $stripe = Transaction::where('payment_mode', $ride_category)->whereDate('created_at','>=', $start_date)->whereDate('created_at','<=',$end_date)->get()->count();
                $orange = Transaction::where('payment_mode', $ride_category)->whereDate('created_at','>=', $start_date)->whereDate('created_at','<=',$end_date)->get()->count();
                return view('admin.transaction-reports.index', compact('start_date', 'end_date', 'ride_category', 'total', 'stripe', 'orange'));
            } else {
                return redirect()->to('admin/transaction-reports');
            }
        }
    }
}
