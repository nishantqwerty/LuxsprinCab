<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookingReportController extends Controller
{
    public function index()
    {
        $total_booking = Booking::get()->count();
        $completed_booking = Booking::where('is_completed', 1)->get()->count();
        $cancelled_booking = Booking::where('is_cancelled', 1)->get()->count();

        return view('admin.booking-reports.index', compact('total_booking', 'completed_booking', 'cancelled_booking'));
    }


    public function date(Request $request)
    {
        if ((isset($request->start_date) && !isset($request->end_date)) || (!isset($request->start_date) && isset($request->end_date))) {
            return back()->with('error', 'Please Provide proper dates.');
        } else {
            if (isset($request->start_date) && isset($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $ride_category = $request->ride_category;
                $total_booking = Booking::where('ride_type', $ride_category)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
                $completed_booking = Booking::where('is_completed', 1)->where('ride_type', $ride_category)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
                $cancelled_booking = Booking::where('is_cancelled', 1)->where('ride_type', $ride_category)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
                return view('admin.booking-reports.index', compact('start_date', 'end_date', 'ride_category', 'total_booking', 'completed_booking', 'cancelled_booking'));
            } else {
                return redirect()->to('admin/booking-reports');
            }
        }
    }
}
