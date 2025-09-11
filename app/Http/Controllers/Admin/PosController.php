<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(){
        return view('admin.pos.index');
    }
}
