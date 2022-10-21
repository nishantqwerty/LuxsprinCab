<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\DriverTotal;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $driver = DriverTotal::with('user')->get(); 
        return view('admin.payout.index', compact('driver'));
    }
}
