<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;
use Validator;
use DB;

class DashboardController extends Controller
{
    public function dashboard(){
        
        return view('admin.dashboard');
    }

    public function dashboardInfo(){
        $now = now(); // current date

        // This month
        $thisMonthSale = DB::table('sale_summary')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('grand_total');

        $thisMonthPurchases = DB::table('purchases')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('grand_total');
        $thisMonthExpenses = DB::table('expense')
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('amount');

        $dashboardInfo = [
            'sale' => $thisMonthSale,
            'purchases' => $thisMonthPurchases,
            'expenses' => $thisMonthExpenses,
        ];

        return response()->json([$dashboardInfo, 'info for dashboard']);
       
    }

    
}
