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
    public function notifications()
    {
        $notifications = DB::table('notification as pn')
            ->join('products as p', 'pn.product_id', '=', 'p.id')
            ->select(
                'pn.id as notification_id',
                'pn.type',
                'pn.created_at as notification_time',
                'p.id as product_id',
                'p.name as product_name',
                'p.barcode',
                'p.product_image as product_image'
            )
            ->orderBy('pn.created_at', 'desc')
            ->get();
        return view('admin.notifications',compact('notifications'));
    }

    public function dashboardInfo(){
        $today = now()->toDateString(); 

        // Today's sales
        $todaySale = DB::table('sale_summary')
            ->whereDate('sale_date', $today)
            ->sum('grand_total');

        // Today's purchases
        $todayPurchases = DB::table('purchases')
            ->whereDate('purchase_date', $today)
            ->sum('grand_total');

        // Today's expenses
        $todayExpenses = DB::table('expense')
            ->whereDate('date', $today)
            ->sum('amount');

        // Net sales (income)
        $sales = DB::table('sale_summary')
            ->whereDate('sale_date', $today)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN document_type IN ('S','PS') THEN grand_total ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN document_type = 'SR' THEN grand_total ELSE 0 END), 0) as net_sales
            ")
            ->first();

        $income = $sales->net_sales;

        $dashboardInfo = [
            'sale' => $todaySale,
            'purchases' => $todayPurchases,
            'expenses' => $todayExpenses,
            'income' => $income,
            'date' => $today,
        ];

        return response()->json([$dashboardInfo, 'info for dashboard']);
    }



}
