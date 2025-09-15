<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(){
        return view('admin.pos.index');
    }
    public function print_view(){
        return view('admin.pos.print_view');
    }
}
