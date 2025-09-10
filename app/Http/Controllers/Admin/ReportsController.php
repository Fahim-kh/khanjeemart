<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Validator;
use DataTables;
class ReportsController extends Controller
{
    //Product sale Report
    public function product_report()
    {
        return view('admin.reports.product_report');
    }


    public function getData(Request $request)
    {
        $query = SaleDetail::select(
            'products.id as product_id',
            'products.name as product_name',
            'products.sku as product_code',
            DB::raw('SUM(sale_details.quantity) as total_sales'),
            DB::raw('SUM(sale_details.subtotal) as total_sales_amount')
        )
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
            ->where('sale_summary.document_type', 'S')
            ->groupBy('products.id', 'products.name', 'products.sku');

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sale_summary.sale_date', [$request->from_date, $request->to_date]);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="#" class="btn btn-sm btn-primary view-details" data-id="' . $row->product_id . '">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getProductDetails(Request $request, $product_id)
    {
        $query = SaleDetail::select(
            'sale_summary.invoice_number',
            'sale_summary.sale_date',
            'customers.name as customer_name',
            'sale_details.quantity',
            'sale_details.selling_unit_price',
            'sale_details.subtotal'
        )
            ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
            ->join('customers', 'sale_summary.customer_id', '=', 'customers.id')
            ->where('sale_details.product_id', $product_id)
            ->where('sale_summary.document_type', 'S');

        // ✅ Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sale_summary.sale_date', [$request->from_date, $request->to_date]);
        } elseif ($request->from_date) {
            $query->whereDate('sale_summary.sale_date', '>=', $request->from_date);
        } elseif ($request->to_date) {
            $query->whereDate('sale_summary.sale_date', '<=', $request->to_date);
        }

        $details = $query->get();

        return response()->json($details);
    }



}
