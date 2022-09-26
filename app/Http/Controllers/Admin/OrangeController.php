<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Models\Chat;
use App\Models\UserChat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrangeController extends Controller
{
    public function cancel(){
        return view('admin.orange.cancel');
    }
}
