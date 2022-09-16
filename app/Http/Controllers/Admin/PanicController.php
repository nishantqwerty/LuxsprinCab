<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Panic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PanicController extends Controller
{
    public function index()
    {
        $panic = Panic::with('user')->get();
        return view('admin.panic.index', compact('panic'));
    }
}
