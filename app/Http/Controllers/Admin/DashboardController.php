<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;
use Validator;

class DashboardController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }
    
}
