<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Validator, Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AboutUs;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->section  = 'Dashboard';
        $this->view     = 'dashboard';
    }

    public function index()
    {
        $user = User::where('user_role', USER)->get()->count();
        $admin = User::where('user_role', DRIVER)->get()->count();
        return view('admin.' . $this->view, compact('user','admin'));
    }
}
