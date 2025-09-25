<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Validator;
use DataTables;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
            ->groupBy('products.id', 'products.name', 'products.barcode', 'units.name');

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



    //expense report

    public function expenseReport()
    {
        return view('admin.reports.expense_report');
    }

    public function getExpenseData(Request $request)
    {
        $query = \DB::table('expense')
            ->join('expense_categories', 'expense.expense_category_id', '=', 'expense_categories.id')
            ->join('users', 'expense.created_by', '=', 'users.id')
            ->leftJoin('warehouses', 'expense.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('accounts', 'expense.account_id', '=', 'accounts.id')
            ->leftJoin('payment_type', 'expense.payment_type_id', '=', 'payment_type.id')
            ->select(
                'expense.id',
                'expense.amount',
                'expense.date',
                'expense.description',
                'expense_categories.name as category_name',
                'users.name as created_by_name',
                'warehouses.name as warehouse_name',
                'accounts.name as account_name',
                'payment_type.name as payment_type_name',
            );

        // 🔎 Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('expense.date', [$request->from_date, $request->to_date]);
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->make(true);
    }

    //expense report close

    //product purchase report
    public function purchaseReport()
    {
        // Supplier list bhejna filter k liye
        $suppliers = \App\Models\Supplier::select('id', 'name')->get();
        return view('admin.reports.purchase_report', compact('suppliers'));
    }

    public function getPurchaseReportData(Request $request)
    {
        $query = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'purchases.id',
                'purchases.purchase_date as date',
                'purchases.invoice_number as reference',
                'suppliers.name as supplier',
                'products.name as product_name',
                DB::raw('SUM(purchase_items.quantity) as qty_purchased'),
                DB::raw('SUM(purchase_items.subtotal) as grand_total')
            )
            ->groupBy('purchases.id', 'purchases.purchase_date', 'purchases.invoice_number', 'suppliers.name', 'products.name')
            ->where('purchases.document_type', "P");
        // 🔎 Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('purchases.purchase_date', [$request->from_date, $request->to_date]);
        }

        // 🔎 Supplier filter
        if ($request->supplier_id) {
            $query->where('purchases.supplier_id', $request->supplier_id);
        }

        return \DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }

    //product purchase report close


    //porduct sale report
    public function saleReport()
    {
        $customers = \App\Models\Customer::select('id', 'name')->get();
        return view('admin.reports.sale_report', compact('customers'));
    }


    public function getSaleReportData(Request $request)
    {
        $query = SaleDetail::select(
            'sale_summary.sale_date as date',
            'sale_summary.invoice_number as reference',
            'sale_summary.id as summery_id',
            'customers.name as customer',
            'products.name as product_name',
            'sale_details.quantity as qty_sold',
            'sale_details.subtotal as grand_total'
        )
            ->join('sale_summary', 'sale_details.sale_summary_id', '=', 'sale_summary.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('customers', 'sale_summary.customer_id', '=', 'customers.id')
            ->whereIn('sale_summary.document_type', ['S', 'PS']);

        // Date filter
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $query->whereBetween('sale_summary.sale_date', [$request->from_date, $request->to_date]);
        }

        // Customer filter (optional)
        if (!empty($request->customer_id)) {
            $query->where('sale_summary.customer_id', $request->customer_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
    //product sale report close


    //customer ledger report

    public function customerLedgerReport()
    {
        $customers = \App\Models\Customer::select('id', 'name')->get();
        return view('admin.reports.customer_ledger_report', compact('customers'));
    }

    public function getCustomerLedgerData(Request $request)
    {
        $customerId = $request->customer_id;
        $from = $request->from_date;
        $to = $request->to_date;

        $customer = \App\Models\Customer::find($customerId);

        $ledgerData = [];
        $balance = 0;

        // Opening Balance
        if ($customer) {
            $balance = $customer->opening_balance ?? 0;
            $ledgerData[] = [
                'date' => null,
                'reference' => '---',
                'description' => 'Opening Balance',
                'debit' => $balance > 0 ? $balance : 0,
                'credit' => $balance < 0 ? abs($balance) : 0,
                'balance' => $balance,
            ];
        }

        // Sales + Sale Returns
        $querySales = \App\Models\Sale::where('customer_id', $customerId);
        if ($from && $to) {
            $querySales->whereBetween('sale_date', [$from, $to]);
        }
        $sales = $querySales->get()->map(function ($txn) {
            return [
                'sale_id' => $txn->id,
                'date' => $txn->sale_date,
                'reference' => $txn->invoice_number,
                'description' => ($txn->document_type == 'S' || $txn->document_type == 'PS'
                    ? 'Sale Invoice'
                    : 'Sale Return')
                    . (!empty($txn->notes) ? ' (' . $txn->notes . ')' : ''),
                'debit' => in_array($txn->document_type, ['S', 'PS']) ? $txn->grand_total : 0,
                'credit' => $txn->document_type == 'SR' ? $txn->grand_total : 0,
                'type' => $txn->document_type,
            ];
        });

        // Payments (PaymentFromCustomer only)
        $queryPayments = \App\Models\PaymentModel::where('customer_id', $customerId)
            ->where('transaction_type', 'PaymentFromCustomer');

        if ($from && $to) {
            $queryPayments->whereBetween('entry_date', [$from, $to]);
        }

        $payments = $queryPayments->get()->map(function ($pay) {
            return [
                'date' => $pay->entry_date,
                'reference' => 'PAY-' . $pay->id,
                'description' => !empty($pay->comments) ? $pay->comments : 'Payment Received',
                'debit' => 0,
                'credit' => $pay->amount,
                'type' => 'P',
            ];
        });

        // Merge Sales + Payments and sort by date
        $transactions = $sales->merge($payments)->sortBy('date');

        foreach ($transactions as $txn) {
            // print_r($txn);
            if ($txn['type'] == 'S' || $txn['type'] == 'PS') {
                $balance += $txn['debit'];
            } elseif ($txn['type'] == 'SR' || $txn['type'] == 'P') {
                $balance -= $txn['credit'];
            }
            $ledgerData[] = [
                'sale_id' => $txn['sale_id'] ?? null,
                'date' => $txn['date'],
                'reference' => $txn['reference'],
                'description' => $txn['description'],
                'debit' => $txn['debit'],
                'credit' => $txn['credit'],
                'balance' => $balance,
            ];
        }
        // die();

        return DataTables::of($ledgerData)
            ->addIndexColumn()
            ->make(true);
    }


    //customer ledger report close


    //stock report
    public function stockReport()
    {
        return view('admin.reports.stock_report');
    }

    public function getStockReportData()
    {
        $products = \App\Models\ProductModel::select('id', 'barcode', 'name')->get();

        $data = [];
        $i = 1;
        foreach ($products as $p) {
            $stock = app(\App\Http\Controllers\Admin\PurchaseController::class)->getProductStock($p->id);

            $data[] = [
                'id' => $p->id,
                'serial' => $i++,
                'code' => $p->barcode,
                'name' => $p->name,
                'stock' => $stock,
            ];
        }

        return \DataTables::of($data)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('stock.report.detail', $row['id']) . '" class="btn btn-sm btn-primary">Detail</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function stockReportDetail($id)
    {
        $product = \App\Models\ProductModel::findOrFail($id);
        return view('admin.reports.stock_report_detail', compact('product'));
    }


    // Purchase
    public function getStockPurchase($id)
    {
        $query = DB::table('purchase_items as pi')
            ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
            ->join('suppliers as s', 'p.supplier_id', '=', 's.id')
            ->join('products as pr', 'pi.product_id', '=', 'pr.id')
            ->where('pi.product_id', $id)
            ->where('p.document_type', 'P')
            ->select(
                'p.purchase_date as date',
                'p.invoice_number as reference',
                's.name as supplier_name',
                'pr.name as product_name',
                'pi.quantity'
            );

        return datatables()->of($query)
            ->filterColumn('supplier_name', function ($query, $keyword) {
                $query->where('s.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('pr.name', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    // Purchase Return
    public function getStockPurchaseReturn($id)
    {
        $query = DB::table('purchase_items as pi')
            ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
            ->join('suppliers as s', 'p.supplier_id', '=', 's.id')
            ->join('products as pr', 'pi.product_id', '=', 'pr.id')
            ->where('pi.product_id', $id)
            ->where('p.document_type', 'PR')
            ->select(
                'p.purchase_date as date',
                'p.invoice_number as reference',
                's.name as supplier_name',
                'pr.name as product_name',
                'pi.quantity'
            );

        return datatables()->of($query)
            ->filterColumn('supplier_name', function ($query, $keyword) {
                $query->where('s.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('pr.name', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    // Sale
    public function getStockSale($id)
    {
        $query = DB::table('sale_details as sd')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->join('customers as c', 'ss.customer_id', '=', 'c.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->where('sd.product_id', $id)
            ->where('ss.document_type', 'S')
            ->select(
                'ss.sale_date as date',
                'ss.invoice_number as reference',
                'c.name as customer_name',
                'p.name as product_name',
                'sd.quantity'
            );

        return datatables()->of($query)
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->where('c.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('p.name', 'like', "%{$keyword}%");
            })
            ->make(true);
    }

    // Sale Return
    // Sale Return
    public function getStockSaleReturn($id)
    {
        $query = DB::table('sale_details as sd')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->join('customers as c', 'ss.customer_id', '=', 'c.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->where('sd.product_id', $id)
            ->where('ss.document_type', 'SR')
            ->select(
                'ss.sale_date as date',
                'ss.invoice_number as reference',
                'c.name as customer_name',
                'p.name as product_name',
                'sd.quantity'
            );

        return datatables()->of($query)
            ->filterColumn('customer_name', function ($query, $keyword) {
                $query->where('c.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('p.name', 'like', "%{$keyword}%");
            })
            ->make(true);
    }



    // Adjustment
    public function getStockAdjustment($id)
    {
        $query = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sai.adjustment_id', '=', 'sa.id')
            ->join('products as pr', 'sai.product_id', '=', 'pr.id')
            ->where('sai.product_id', $id)
            ->select(
                'sa.adjustment_date as date',
                'sa.id as reference',
                'pr.name as product_name',
                'sai.adjustment_type',
                'sai.quantity'
            );

        return datatables()->of($query)
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('pr.name', 'like', "%{$keyword}%");
            })
            ->make(true);
    }
    //stock report close


    //profit and loss report
    public function profitLoss(Request $request)
    {
        // dates (defaults = current month)
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->endOfMonth()->toDateString();

        // normalize to full day range
        $fromDT = Carbon::parse($from)->startOfDay();
        $toDT = Carbon::parse($to)->endOfDay();

        // SALES (count + total grand_total) - document_type S and PS
        $sales_count = DB::table('sale_summary')
            ->whereIn('document_type', ['S', 'PS'])
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->count();

        $sales_total = DB::table('sale_summary')
            ->whereIn('document_type', ['S', 'PS'])
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->sum('grand_total');

        // SALES RETURN
        $sales_return_count = DB::table('sale_summary')
            ->where('document_type', 'SR')
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->count();

        $sales_return_total = DB::table('sale_summary')
            ->where('document_type', 'SR')
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->sum('grand_total');

        // PURCHASES
        $purchase_count = DB::table('purchases')
            ->where('document_type', 'P')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->count();

        $purchase_total = DB::table('purchases')
            ->where('document_type', 'P')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->sum('grand_total');

        // PURCHASE RETURN
        $purchase_return_count = DB::table('purchases')
            ->where('document_type', 'PR')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->count();

        $purchase_return_total = DB::table('purchases')
            ->where('document_type', 'PR')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->sum('grand_total');

        // EXPENSES
        $expenses_total = DB::table('expense')
            ->whereBetween('date', [$fromDT, $toDT])
            ->sum('amount');

        // PAYMENTS
        $payments_received = DB::table('payments')
            ->where('transaction_type', 'PaymentFromCustomer')
            ->whereBetween('entry_date', [$fromDT, $toDT])
            ->sum('amount');

        $payments_sent = DB::table('payments')
            ->where('transaction_type', 'PaymentToVendor')
            ->whereBetween('entry_date', [$fromDT, $toDT])
            ->sum('amount');

        $payments_net = $payments_received - $payments_sent;

        // REVENUE (sales - sales return)
        $revenue_total = $sales_total - $sales_return_total;

        // COGS (use sale_details.cost_unit_price * quantity)
        $cogs_total = DB::table('sale_details')
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->select(DB::raw('COALESCE(SUM(quantity * cost_unit_price),0) as total'))
            ->value('total') ?? 0;

        // For this implementation we use sale_details.cost_unit_price as both FIFO and Average cost baseline.
        // (If you need true FIFO/Avg you must implement inventory valuation logic.)
        $fifo_cost = $cogs_total;
        $avg_cost = $cogs_total;

        $profit_fifo = $revenue_total - $fifo_cost;
        $profit_avg = $revenue_total - $avg_cost;

        return view('admin.reports.profit_loss', compact(
            'from',
            'to',
            'sales_count',
            'sales_total',
            'purchase_count',
            'purchase_total',
            'sales_return_count',
            'sales_return_total',
            'purchase_return_count',
            'purchase_return_total',
            'expenses_total',
            'revenue_total',
            'cogs_total',
            'fifo_cost',
            'avg_cost',
            'profit_fifo',
            'profit_avg',
            'payments_received',
            'payments_sent',
            'payments_net'
        ));
    }
    //profit and loss report close

    //income and expense
    public function chartData(Request $request)
    {
        $year = $request->year ?? now()->year;
        $income = [];
        $expenses = [];
        $totalIncome = 0;
        $totalExpenses = 0;

        for ($month = 1; $month <= 12; $month++) {
            $fromDT = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $toDT = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $sales_total = DB::table('sale_summary')
                ->whereIn('document_type', ['S', 'PS'])
                ->whereBetween('sale_date', [$fromDT, $toDT])
                ->sum('grand_total');

            $sales_return_total = DB::table('sale_summary')
                ->where('document_type', 'SR')
                ->whereBetween('sale_date', [$fromDT, $toDT])
                ->sum('grand_total');

            $net_sales = $sales_total - $sales_return_total;

            $expense_total = DB::table('expense')
                ->whereBetween('date', [$fromDT, $toDT])
                ->sum('amount');

            $income[] = (float) $net_sales;
            $expenses[] = (float) $expense_total;

            $totalIncome += $net_sales;
            $totalExpenses += $expense_total;
        }

        // % change income vs expense (basic example)
        $percentIncome = $totalIncome > 0 ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2) : 0;
        $percentExpense = $totalExpenses > 0 ? round((($totalExpenses - $totalIncome) / $totalExpenses) * 100, 2) : 0;

        return response()->json([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'series' => [
                ['name' => 'Income', 'data' => $income],
                ['name' => 'Expenses', 'data' => $expenses],
            ],
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpenses' => $totalExpenses,
                'percentIncome' => $percentIncome,
                'percentExpense' => $percentExpense,
            ]
        ]);
    }


    //income and expense close


    public function overallReport(Request $request)
    {
        $filter = $request->filter ?? 'yearly'; // default Yearly
        $now = Carbon::now();

        if ($filter === 'yearly') {
            $fromDT = $now->copy()->startOfYear();
            $toDT = $now->copy()->endOfYear();
        } elseif ($filter === 'monthly') {
            $fromDT = $now->copy()->startOfMonth();
            $toDT = $now->copy()->endOfMonth();
        } elseif ($filter === 'weekly') {
            $fromDT = $now->copy()->startOfWeek();
            $toDT = $now->copy()->endOfWeek();
        } elseif ($filter === 'today') {
            $fromDT = $now->copy()->startOfDay();
            $toDT = $now->copy()->endOfDay();
        }

        // echo $fromDT;
        // echo "<br>";
        // echo $toDT;
        // Purchase
        $purchase_total = DB::table('purchases')
            ->where('document_type', 'P')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->sum('grand_total');

        $purchase_return_total = DB::table('purchases')
            ->where('document_type', 'PR')
            ->whereBetween('purchase_date', [$fromDT, $toDT])
            ->sum('grand_total');

        $net_purchase = $purchase_total - $purchase_return_total;

        // Sales (minus return)
        $sales_total = DB::table('sale_summary')
            ->whereIn('document_type', ['S', 'PS'])
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->sum('grand_total');

        $sales_return_total = DB::table('sale_summary')
            ->where('document_type', 'SR')
            ->whereBetween('sale_date', [$fromDT, $toDT])
            ->sum('grand_total');

        $net_sales = $sales_total - $sales_return_total;

        // Expense
        $expense_total = DB::table('expense')
            ->whereBetween('date', [$fromDT, $toDT])
            ->sum('amount');

        // Gross Profit
        $gross_profit = $net_purchase - ($net_sales + $expense_total);

        return response()->json([
            'series' => [
                (float) $net_purchase,
                (float) $net_sales,
                (float) $expense_total,
                (float) $gross_profit,
            ],
            'labels' => ['Purchase', 'Sales', 'Expense', 'Gross Profit']
        ]);
    }


    public function chartDataPurchaseSaleWeek(Request $request)
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek();     // Sunday

        $purchase = [];
        $sales = [];
        $labels = [];

        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);

        foreach ($period as $date) {
            // PURCHASE total
            $purchase_total = DB::table('purchases')
                ->whereDate('purchase_date', $date)
                ->sum('grand_total');

            // SALES total
            $sales_total = DB::table('sale_summary')
                ->whereIn('document_type', ['S', 'PS'])
                ->whereDate('sale_date', $date)
                ->sum('grand_total');

            $purchase[] = (float) $purchase_total;
            $sales[] = (float) $sales_total;
            $labels[] = $date->format('D'); // Mon, Tue, Wed...
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                [
                    'name' => 'Purchase',
                    'data' => $purchase
                ],
                [
                    'name' => 'Sales',
                    'data' => $sales
                ]
            ],
            'totals' => [
                'purchase' => array_sum($purchase),
                'sales' => array_sum($sales),
            ]
        ]);

    }

    public function outOfStockProducts()
{
    $products = DB::table('products')
        ->select('id', 'name', 'barcode')
        ->get();

    $outOfStock = [];

    foreach ($products as $product) {
        $stock = app(\App\Http\Controllers\Admin\PurchaseController::class)->getProductStock($product->id);

        if ($stock <= 0) {
            $outOfStock[] = [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
            ];
        }
    }

    return response()->json([
        'data' => $outOfStock
    ]);
}




}
