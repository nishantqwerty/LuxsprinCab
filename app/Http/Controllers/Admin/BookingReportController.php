<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingReportController extends Controller
{
    public function index()
    {
        $total_booking = Booking::get()->count();
        $completed_booking = Booking::where('is_completed', 1)->get()->count();
        $cancelled_booking = Booking::where('is_cancelled', 1)->get()->count();

        return view('admin.booking-reports.index', compact('total_booking', 'completed_booking', 'cancelled_booking'));
    }

    public function data(Request $request)
    {
        if (isset($request->start_date) && isset($request->end_date)) {
            $total_booking = Booking::whereBetween('create_at', [$request->start_date, $request->end_date])->get()->count();
            $completed_booking = Booking::where('is_completed', 1)->whereBetween('create_at', [$request->start_date, $request->end_date])->get()->count();
            $cancelled_booking = Booking::where('is_cancelled', 1)->whereBetween('create_at', [$request->start_date, $request->end_date])->get()->count();
            return response(200);
        }
    }
}
