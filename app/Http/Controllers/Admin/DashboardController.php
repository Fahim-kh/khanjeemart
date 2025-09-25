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
    public function dashboard()
    {

        return view('admin.dashboard');
    }

    public function dashboardInfo()
    {
        $now = now(); // current date

        // This month
        $thisMonthSale = DB::table('sale_summary')
            ->whereYear('sale_date', $now->year)
            ->whereMonth('sale_date', $now->month)
            ->sum('grand_total');

        $thisMonthPurchases = DB::table('purchases')
            ->whereYear('purchase_date', $now->year)
            ->whereMonth('purchase_date', $now->month)
            ->sum('grand_total');

        $thisMonthExpenses = DB::table('expense')
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->sum('amount');

        $sales = DB::table('sale_summary')
            ->whereYear('sale_date', $now->year)
            ->whereMonth('sale_date', $now->month)
            ->selectRaw("
            COALESCE(SUM(CASE WHEN document_type IN ('S','PS') THEN grand_total ELSE 0 END), 0)
            - COALESCE(SUM(CASE WHEN document_type = 'SR' THEN grand_total ELSE 0 END), 0) as net_sales
        ")
            ->first();

        $income = $sales->net_sales;

        $dashboardInfo = [
            'sale' => $thisMonthSale,
            'purchases' => $thisMonthPurchases,
            'expenses' => $thisMonthExpenses,
            'income' => $income
        ];

        return response()->json([$dashboardInfo, 'info for dashboard']);

    }


}
