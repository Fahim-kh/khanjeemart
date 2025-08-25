<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Validator;
use DataTables;
class SaleController extends Controller
{
    public function index()
    {
        return view('admin.sale.index');
    }

    public function create()
    {
        DB::table('sale_details_temp')->where('sale_summary_id', '!=', 999)->delete();
        $customers = Customer::where('status',1)->get();
        return view('admin.sale.create',compact('customers'));
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
   public function show($id)
   {
        try {
                $sales = DB::table('sale_summary')
                        ->select(
                            'sale_summary.id',
                            'sale_summary.sale_date',
                            'sale_summary.invoice_number',
                            'sale_summary.grand_total',
                            'sale_summary.status',
                            'customers.name as customer_name'
                        )
                        ->leftJoin('customers', 'customers.id', '=', 'sale_summary.customer_id')
                        ->where('sale_summary.document_type', 'S') // Only normal Sale, skip SR (Sale Return)
                        ->orderBy('sale_summary.id', 'desc')
                        ->get();

                    return DataTables::of($sales)
                        ->addIndexColumn()
                        ->addColumn('action', function ($data) {
                            return table_action_dropdown_sale($data->id, 'sale', 'Sale');
                        })
                        ->rawColumns(['action'])
                        ->make(true);

                } catch (\Exception $e) {
                    dd($e->getMessage());
                }

    }

    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // $data = DB::table('purchase_items_temp')
            // ->join('products', 'products.id', '=', 'purchase_items_temp.product_id')
            // ->where('purchase_items_temp.id', $id)
            // ->select(
            //     'purchase_items_temp.*',
            //     'products.name as product_name'
            // )
            // ->first();
            // return response()->json(['success' => 'successfull retrieve data', 'data' => json_encode($data)], 200);
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = DB::table('sale_details_temp')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['success' => 'Sale item deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Record not found.'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function StoreSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                    function ($attribute, $value, $fail) {
                        $exists = DB::table('sale_details_temp')
                            ->where('product_id', $value)
                            ->exists();

                        if ($exists) {
                            $fail('This product is already exist in sale temp.');
                        }
                    },
                ],
                'date' => 'required|date',
                'quantity' => 'required|numeric',
                'unit_cost' => 'required|numeric|min:0',
                'sell_price' => 'required|numeric|min:0',
                //'warehouse_id' => 'required|integer|exists:warehouses,id',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $subtotal = $request->quantity * $request->sell_price;

            // Insert into sale_details_temp
            DB::table('sale_details_temp')->insert([
                'sale_summary_id' => 999,
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'cost_unit_price' => $request->unit_cost,
                'selling_unit_price' => $request->sell_price,
                'subtotal' => $subtotal,
                'sale_date' => $request->date,
                'warehouse_id' => null,
                'created_by' => auth()->id(), // user track karne ke liye
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json(['success' => 'Product successfully added into sale_details_temp.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function UpdateSaleItem(Request $request)
    {
        try {
            $id = $request->id;

            // record nikalna
            $item = DB::table('sale_details_temp')->where('id', $id)->first();
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'error' => 'Item not found'
                ]);
            }

            // nayi values (agar nahi bheji gayi to purani hi rakh lo)
            $quantity = $request->has('quantity') ? (int) $request->quantity : $item->quantity;
            $price    = $request->has('selling_unit_price') ? (float) $request->selling_unit_price : $item->selling_unit_price;

            $subtotal = $quantity * $price;

            // update karna
            DB::table('sale_details_temp')
                ->where('id', $id)
                ->update([
                    'quantity'           => $quantity,
                    'selling_unit_price' => $price,
                    'subtotal'           => $subtotal,
                    'updated_at'         => now(),
                ]);

            return response()->json([
                'success'  => true,
                'subtotal' => $subtotal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ]);
        }
    }


    public function getSaleView()
    {
        try {
            $data = DB::table('sale_details_temp')
                    ->join('products as product', 'sale_details_temp.product_id', '=', 'product.id')
                    ->select(
                        'sale_details_temp.*',
                        'product.name as productName',
                        'product.product_image as productImg'
                    )
                    ->get();

            return response()->json([
                'success' => 'Successfully retrieved data',
                'data' => $data->toJson()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function storeFinalSale(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'sale_date'     => 'required|date',
                'customer_id'   => 'required|integer|exists:customers,id', 
                //'customer_name' => 'required|string|max:100',
                'reference'=> 'required|string|unique:sale_summary,invoice_number',
                //'customer_type' => 'required|in:cash,credit',
                'order_tax'     => 'nullable|numeric',
                'discount'      => 'nullable|numeric',
                'shipping'      => 'nullable|numeric',
                'status'        => 'required',
                'note'          => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch sale items from temporary table (user/session wise)
            $tempItems = DB::table('sale_details_temp')
                ->where('created_by', auth()->id())
                ->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary sale table.'], 400);
            }

            // Calculate total from temp items
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount   = $request->discount ?? 0;
            $taxPercent = $request->order_tax ?? 0;
            $taxCalc    = ($totalAmount * $taxPercent) / 100; 
            $shipping   = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

            // Insert into sale_summary
            $saleId = DB::table('sale_summary')->insertGetId([
                'created_by'      => auth()->id(),
                'store_id'        => $request->store_id ?? null,
                'customer_id'     => $request->customer_id,
                'document_type'   => "S",
                'customer_type'   => "cash",
                'invoice_number'  => $request->reference,
                'customer_name'   => $request->customer_name,
                'sale_date'       => $request->sale_date,
                'total_amount'    => $totalAmount,
                'discount'        => $discount,
                'tax'             => $taxPercent,
                'shipping_charge' => $shipping,
                'grand_total'     => $grandTotal,
                'notes'           => $request->note,
                'status'          => $request->status,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Insert items into sale_details
            foreach ($tempItems as $item) {
                DB::table('sale_details')->insert([
                    'sale_summary_id'   => $saleId,
                    'product_id'        => $item->product_id,
                    'warehouse_id'      => $item->warehouse_id,
                    'quantity'          => $item->quantity,
                    'cost_unit_price'   => $item->cost_unit_price,
                    'selling_unit_price'=> $item->selling_unit_price,
                    'subtotal'          => $item->subtotal,
                    'sale_date'         => $request->sale_date,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            // Clear temp table (current user/session only)
            DB::table('sale_details_temp')
                ->where('created_by', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'success' => 'Sale successfully saved.',
                'sale_id' => $saleId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

}
