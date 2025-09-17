<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use DataTables;
class PosController extends Controller
{
    public function index(){
        return view('admin.pos.index');
    }
    public function print_view(){
        return view('admin.pos.print_view');
    }

    public function getSalePrint($id)
    {
        // Summary data
        $summary = DB::table('sale_summary as ss')
            ->join('customers as c', 'ss.customer_id', '=', 'c.id')
            ->select(
                'ss.*',
                'c.name as customer_name',
                'c.phone as customer_phone'
            )
            ->where('ss.id', $id)
            ->first();

        // Details data
        $details = DB::table('sale_details as sd')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->select(
                'p.name as product_name',
                'p.barcode as barcode',
                    DB::raw('RIGHT(p.barcode, 4) as barcode_last4'),
                'sd.quantity',
                'sd.selling_unit_price as unit_price',
                DB::raw('(sd.quantity * sd.selling_unit_price) as subtotal')
            )
            ->where('sd.sale_summary_id', $id)
            ->get();

        return response()->json([
            'summary' => $summary,
            'details' => $details
        ]);
    }
}
