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
            'products.barcode as product_barcode',
            'units.name as unit_name',
            DB::raw('SUM(sale_details.quantity) as total_sales'),
            DB::raw('SUM(sale_details.subtotal) as total_sales_amount')
        )
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id')
            ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
            ->where('sale_summary.document_type', 'S')
            ->groupBy('products.id', 'products.name', 'products.barcode','units.name');

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
            'users.name as created_by_name',
            // 'sale_summary.sale_date',
            DB::raw('DATE(sale_summary.sale_date) as sale_date'),
            'customers.name as customer_name',
            'sale_details.quantity',
            'sale_details.selling_unit_price',
            'sale_details.subtotal',
            'units.name as unit_name',
            'products.name as product_name',
        )
            ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
            ->join('users', 'users.id', '=', 'sale_summary.created_by')
            ->join('customers', 'sale_summary.customer_id', '=', 'customers.id')
            ->where('sale_details.product_id', $product_id)
            ->where('sale_summary.document_type', 'S')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('units', 'products.unit_id', '=', 'units.id');

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

    public function downloadSummaryPdf(Request $request)
    {
        $query = SaleDetail::select(
            'products.id as product_id',
            'products.name as product_name',
            'products.barcode as product_barcode',
            'units.name as unit_name',
            DB::raw('SUM(sale_details.quantity) as total_sales'),
            DB::raw('SUM(sale_details.subtotal) as total_sales_amount')
        )
        ->join('products', 'sale_details.product_id', '=', 'products.id')
        ->join('units', 'products.unit_id', '=', 'units.id')
        ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
        ->where('sale_summary.document_type', 'S')
        ->groupBy('products.id', 'products.name', 'products.barcode', 'units.name');

        // Apply filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sale_summary.sale_date', [$request->from_date, $request->to_date]);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('admin.reports.product-report-summary-pdf', compact('data'));
        return $pdf->download('product_summary_report.pdf');
    }

    public function downloadDetailPdf($id, Request $request)
    {
        $query = SaleDetail::select(
            'sale_summary.invoice_number',
            'users.name as created_by_name',
            DB::raw('DATE(sale_summary.sale_date) as sale_date'),
            'customers.name as customer_name',
            'sale_details.quantity',
            'units.name as unit_name',
            'sale_details.selling_unit_price',
            'sale_details.subtotal',
            'products.name as product_name',
        )
        ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
        ->join('users', 'users.id', '=', 'sale_summary.created_by')
        ->join('customers', 'sale_summary.customer_id', '=', 'customers.id')
        ->join('products', 'sale_details.product_id', '=', 'products.id')
        ->join('units', 'products.unit_id', '=', 'units.id')
        ->where('sale_details.product_id', $id)
        ->where('sale_summary.document_type', 'S');

        // Apply filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sale_summary.sale_date', [$request->from_date, $request->to_date]);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('admin.reports.product-report-detail-pdf', compact('data'));
        return $pdf->download('product_detail_report.pdf');
    }


}
