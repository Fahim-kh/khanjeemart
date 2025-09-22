<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use App\Models\Customer;
use App\Models\Supplier;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::where('status', 1)->get();
        $suppliers = Supplier::where('status', 1)->get();
        return view('admin.payment.index', compact('customers', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_type' => 'required|in:PaymentFromCustomer,PaymentToVendor',
                'trans_mode' => 'required|in:cash,cheque,bank',
                'amount' => 'required|numeric|min:1',
                'entry_date' => 'required|date',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->transaction_type === 'PaymentFromCustomer') {
                    $totalSale = \DB::table('sale_summary')
                        ->where('customer_id', $request->customer_id)
                        ->whereIn('document_type', ['S', 'PS'])
                        ->sum('grand_total');

                    $totalSaleReturn = \DB::table('sale_summary')
                        ->where('customer_id', $request->customer_id)
                        ->where('document_type', 'SR')
                        ->sum('grand_total');

                    $alreadyReceived = \DB::table('payments')
                        ->where('customer_id', $request->customer_id)
                        ->where('transaction_type', 'PaymentFromCustomer')
                        ->sum('amount');

                    $remaining = ($totalSale - $totalSaleReturn) - $alreadyReceived;

                    if ($request->amount > $remaining) {
                        $validator->errors()->add('amount', "Amount exceeds remaining balance. Remaining = $remaining");
                    }
                }

                if ($request->transaction_type === 'PaymentToVendor') {
                    $totalPurchase = \DB::table('purchases')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('document_type', 'P')
                        ->sum('grand_total');

                    $totalPurchaseReturn = \DB::table('purchases')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('document_type', 'PR')
                        ->sum('grand_total');

                    $alreadyPaid = \DB::table('payments')
                        ->where('supplier_id', $request->supplier_id)
                        ->where('transaction_type', 'PaymentToVendor')
                        ->sum('amount');

                    $remaining = ($totalPurchase - $totalPurchaseReturn) - $alreadyPaid;

                    if ($request->amount > $remaining) {
                        $validator->errors()->add('amount', "Amount exceeds remaining balance. Remaining = $remaining");
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // ✅ Save Payment
            $payment = Payment::create([
                'transaction_type' => $request->post('transaction_type'),
                'trans_mode' => $request->post('trans_mode'),
                'supplier_id' => $request->post('supplier_id'),
                'customer_id' => $request->post('customer_id'),
                'amount' => $request->post('amount'),
                'cheque_no' => $request->post('cheque_no'),
                'cheque_date' => $request->post('cheque_date'),
                'received_from' => $request->post('received_from'),
                'payee_from' => $request->post('payee_from'),
                'comments' => $request->post('comments'),
                'entry_date' => $request->post('entry_date'),
            ]);

            return response()->json([
                'success' => 'Payment created successfully',
                'data' => $payment
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Display listing for datatable.
     */
    public function show()
    {
        try {
            $payments = Payment::select(
                'payments.id',
                'payments.entry_date',
                'payments.transaction_type',
                'payments.trans_mode',
                'payments.amount',
                'payments.comments',
                'suppliers.name as supplier',   // alias as supplier
                'customers.name as customer'    // alias as customer
            )
                ->leftJoin('suppliers', 'payments.supplier_id', '=', 'suppliers.id')
                ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
                ->orderBy('payments.id', 'desc');

            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_delete_button($data->id, 'payment');
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    /**
     * Edit single payment record.
     */
    public function edit(string $id)
    {
        try {
            $payment = Payment::with(['supplier', 'customer'])->find($id);
            return response()->json(['success' => 'Successfully retrieved data', 'data' => $payment], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update payment record.
     */
    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_type' => 'required|in:PaymentFromCustomer,PaymentToVendor',
                'trans_mode' => 'required|in:cash,cheque,bank',
                'amount' => 'required|numeric|min:1',
                'entry_date' => 'required|date',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $payment = Payment::findOrFail($request->id);
            $payment->transaction_type = $request->transaction_type;
            $payment->trans_mode = $request->trans_mode;
            $payment->supplier_id = $request->supplier_id;
            $payment->customer_id = $request->customer_id;
            $payment->amount = $request->amount;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_date = $request->cheque_date;
            $payment->received_from = $request->received_from;
            $payment->payee_from = $request->payee_from;
            $payment->comments = $request->comments;
            $payment->entry_date = $request->entry_date;
            $payment->update();

            return response()->json(['success' => 'Payment successfully updated'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete payment record.
     */
    public function destroy(string $id)
    {
        try {
            $payment = Payment::find($id);
            $payment->delete();
            return response()->json(['success' => 'Payment successfully deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
