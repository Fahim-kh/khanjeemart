<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Validator;
use DataTables;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index()
    {
        return view('admin.sale.index');
    }
    public function pos_index()
    {
        return view('admin.sale.view_pos_sale');
    }

    public function create()
    {
        $customers = Customer::where('status', 1)->get();
        return view('admin.sale.create', compact('customers'));
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
            // $sales = DB::table('sale_summary')
            //     ->select(
            //         'sale_summary.id',
            //         'sale_summary.sale_date',
            //         'sale_summary.invoice_number',
            //         'sale_summary.grand_total',
            //         'sale_summary.status',
            //         'customers.name as customer_name'
            //     )
            //     ->leftJoin('customers', 'customers.id', '=', 'sale_summary.customer_id')
            //     ->where('sale_summary.document_type', 'S') // Only normal Sale, skip SR (Sale Return)
            //     ->orderBy('sale_summary.id', 'desc');
            $sales = DB::table('sale_summary')
                ->select(
                    'sale_summary.id',
                    'sale_summary.sale_date',
                    'sale_summary.invoice_number',
                    'sale_summary.grand_total',
                    'sale_summary.status',
                    'customers.name as customer_name',
                    DB::raw('CASE WHEN EXISTS (
                                    SELECT 1 
                                    FROM sale_summary as sr 
                                    WHERE sr.ref_document_no = sale_summary.id 
                                    AND sr.document_type = "SR"
                    ) THEN 1 ELSE 0 END as has_return')
                )
                ->leftJoin('customers', 'customers.id', '=', 'sale_summary.customer_id')
                ->where('sale_summary.document_type', 'S')
                ->orderBy('sale_summary.id', 'desc');



            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_action_dropdown_sale($data->id, 'sale', 'Sale');
                })
                ->addColumn('has_return', function ($data) {
                    return $data->has_return; // send to frontend
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }


    public function getPosSale()
    {
        try {
            $sales = DB::table('sale_summary')
                ->select(
                    'sale_summary.id',
                    'sale_summary.sale_date',
                    'sale_summary.invoice_number',
                    'sale_summary.grand_total',
                    'sale_summary.status',
                    'customers.name as customer_name',
                    DB::raw('CASE WHEN EXISTS (
                                    SELECT 1 
                                    FROM sale_summary as sr 
                                    WHERE sr.ref_document_no = sale_summary.id 
                                    AND sr.document_type = "SR"
                    ) THEN 1 ELSE 0 END as has_return')
                )
                ->leftJoin('customers', 'customers.id', '=', 'sale_summary.customer_id')
                ->where('sale_summary.document_type', 'PS')
                ->orderBy('sale_summary.id', 'desc');

            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return view_action_button($data->id, 'sale_pos', 'POS Sale');
                })
                ->addColumn('has_return', function ($data) {
                    return $data->has_return; // send to frontend
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
    public function pos_destroy(string $id)
    {
        try {
            $deleted = DB::table('pos_sale_details_temp')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['success' => 'Item removed successfully'], 200);
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
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = DB::table('sale_details_temp')
                            ->where('product_id', $value)
                            ->where('sale_summary_id', $request->sale_id)
                            ->where('created_by', auth()->id())
                            ->exists();

                        if ($exists) {
                            $fail('This product is already exist in sale temp.');
                        }
                    },
                    function ($attribute, $value, $fail) use ($request) {
                        // Stock check
                        $stock = app(\App\Http\Controllers\Admin\PurchaseController::class)
                            ->getProductStock($value);

                        if ($stock < $request->quantity) {
                            $fail("Not enough stock available. Current stock: {$stock}");
                        }
                    },
                ],
                'date' => 'required|date',
                'quantity' => 'required|numeric',
                'unit_cost' => 'required|numeric|min:0',
                'sell_price' => 'required|numeric|min:0',
                'customer_id' => 'required|integer|exists:customers,id',
                //'warehouse_id' => 'required|integer|exists:warehouses,id',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $subtotal = $request->quantity * $request->sell_price;

            // Insert into sale_details_temp
            DB::table('sale_details_temp')->insert([
                'sale_summary_id' => $request->sale_id ?? 999,
                'product_id' => $request->product_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'cost_unit_price' => $request->unit_cost,
                'selling_unit_price' => $request->sell_price,
                'subtotal' => $subtotal,
                'sale_date' => $request->date,
                'customer_id' => $request->customer_id,
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
    public function posStoreSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $detail = DB::table('pos_sale_details_temp')
                ->where('product_id', $request->product_id)
                ->where('sale_summary_id', $request->sale_id)
                ->where('created_by', auth()->id())
                ->first();

            $quantity = $detail ? $detail->quantity + 1 : 1;
            $validator = Validator::make($request->all(), [
                'product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                    function ($attribute, $value, $fail) use ($request, $quantity) {
                        // Stock check
                        $stock = app(\App\Http\Controllers\Admin\PurchaseController::class)
                            ->getProductStock($request->product_id);

                        if ($stock < $quantity) {
                            $fail("Not enough stock available. Current stock: {$stock}");
                        }
                    },
                ],
            ]);


            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }


            if ($detail) {
                $newQty = $detail->quantity + 1;
                $newSubtotal = $newQty * $request->sell_price;

                // update quantity
                DB::table('pos_sale_details_temp')
                    ->where('id', $detail->id)
                    ->update([
                        'quantity' => $newQty,
                        'subtotal' => $newSubtotal,
                        'updated_at' => now(),
                    ]);

                DB::commit();

            } else {
                $subtotal = 1 * $request->sell_price;

                // Insert into sale_details_temp
                DB::table('pos_sale_details_temp')->insert([
                    'sale_summary_id' => $request->sale_id ?? 999,
                    'product_id' => $request->product_id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => ($request->quantity) ? $request->quantity : 1,
                    'cost_unit_price' => $request->unit_cost,
                    'selling_unit_price' => $request->sell_price,
                    'subtotal' => $subtotal,
                    'sale_date' => Carbon::now(),
                    'customer_id' => $request->customer_id,
                    'warehouse_id' => null,
                    'created_by' => auth()->id(), // user track karne ke liye
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::commit();


            }

            return response()->json(['success' => 'Product successfully added into pos_sale_details_temp.'], 200);

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
            $price = $request->has('selling_unit_price') ? (float) $request->selling_unit_price : $item->selling_unit_price;

            $subtotal = $quantity * $price;

            // update karna
            DB::table('sale_details_temp')
                ->where('id', $id)
                ->update([
                    'quantity' => $quantity,
                    'selling_unit_price' => $price,
                    'subtotal' => $subtotal,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'subtotal' => $subtotal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function posUpdateSaleItem(Request $request)
    {
        try {
            $id = $request->id;
            // record nikalna
            $item = DB::table('pos_sale_details_temp')->where('id', $id)->first();
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'error' => 'Item not found'
                ]);
            }

            $quantity = $request->has('quantity') ? (int) $request->quantity : $item->quantity;
            $product_id = $item->product_id;
            $validator = Validator::make($request->all(), []); // koi field rule nahi

            $validator->after(function ($validator) use ($product_id, $quantity) {
                $stock = app(\App\Http\Controllers\Admin\PurchaseController::class)
                    ->getProductStock($product_id);

                if ($stock < $quantity) {
                    $validator->errors()->add('stock', "Not enough stock available. Current stock: {$stock}");
                }
            });

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }


            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }


            $price = $request->has('selling_unit_price') ? (float) $request->selling_unit_price : $item->selling_unit_price;

            $subtotal = $quantity * $price;

            DB::table('pos_sale_details_temp')
                ->where('id', $id)
                ->update([
                    'quantity' => $quantity,
                    'selling_unit_price' => $price,
                    'subtotal' => $subtotal,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'subtotal' => $subtotal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getSaleView($sale_id)
    {
        try {
            $data = DB::select("
            SELECT 
                sdt.id,
                sdt.product_id,
                sdt.quantity,
                sdt.selling_unit_price,
                sdt.subtotal,
                sdt.customer_id,
                p.name AS productName,
                p.barcode AS productbarcode,
                p.product_image AS productImg,
                
                (
                    COALESCE(ps.purchased_qty, 0)
                    - COALESCE(ps.returned_qty, 0)
                    - COALESCE(ss.sold_qty, 0)
                    - COALESCE(ss.sold_qty_ps, 0)
                    + COALESCE(ss.sale_return_qty, 0)
                    + COALESCE(sa.adjustment_addition, 0)
                    - COALESCE(sa.adjustment_subtraction, 0)
                ) AS stock

            FROM sale_details_temp sdt
            JOIN products p ON sdt.product_id = p.id

            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    SUM(CASE WHEN pu.document_type = 'P' THEN pi.quantity ELSE 0 END) AS purchased_qty,
                    SUM(CASE WHEN pu.document_type = 'PR' THEN pi.quantity ELSE 0 END) AS returned_qty
                FROM purchase_items pi
                JOIN purchases pu ON pu.id = pi.purchase_id
                GROUP BY pi.product_id
            ) ps ON ps.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sd.product_id,
                    SUM(CASE WHEN ss.document_type = 'S' THEN sd.quantity ELSE 0 END) AS sold_qty,
                    SUM(CASE WHEN ss.document_type = 'PS' THEN sd.quantity ELSE 0 END) AS sold_qty_ps,
                    SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END) AS sale_return_qty
                FROM sale_details sd
                JOIN sale_summary ss ON ss.id = sd.sale_summary_id
                GROUP BY sd.product_id
            ) ss ON ss.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sai.product_id,
                    SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END) AS adjustment_addition,
                    SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END) AS adjustment_subtraction
                FROM stock_adjustment_items sai
                JOIN stock_adjustments sa ON sa.id = sai.adjustment_id
                GROUP BY sai.product_id
            ) sa ON sa.product_id = p.id

            WHERE sdt.sale_summary_id = $sale_id 
              AND sdt.created_by = '" . auth()->id() . "'
            ORDER BY sdt.id DESC
        ");
            $data = collect($data);
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


    public function getSaleViewEdit($sale_id)
    {
        try {
            $data = DB::select("
            SELECT 
                sdt.id,
                sdt.product_id,
                sdt.quantity,
                sdt.edit_stock,
                sdt.selling_unit_price,
                sdt.subtotal,
                sdt.customer_id,
                p.name AS productName,
                p.barcode AS productbarcode,
                p.product_image AS productImg,
                
                (
                    COALESCE(ps.purchased_qty, 0)
                    - COALESCE(ps.returned_qty, 0)
                    - COALESCE(ss.sold_qty, 0)
                    - COALESCE(ss.sold_qty_ps, 0)
                    + COALESCE(ss.sale_return_qty, 0)
                    + COALESCE(sa.adjustment_addition, 0)
                    - COALESCE(sa.adjustment_subtraction, 0)
                ) AS stock

            FROM sale_details_temp sdt
            JOIN products p ON sdt.product_id = p.id

            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    SUM(CASE WHEN pu.document_type = 'P' THEN pi.quantity ELSE 0 END) AS purchased_qty,
                    SUM(CASE WHEN pu.document_type = 'PR' THEN pi.quantity ELSE 0 END) AS returned_qty
                FROM purchase_items pi
                JOIN purchases pu ON pu.id = pi.purchase_id
                GROUP BY pi.product_id
            ) ps ON ps.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sd.product_id,
                    SUM(CASE WHEN ss.document_type = 'S' THEN sd.quantity ELSE 0 END) AS sold_qty,
                    SUM(CASE WHEN ss.document_type = 'PS' THEN sd.quantity ELSE 0 END) AS sold_qty_ps,
                    SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END) AS sale_return_qty
                FROM sale_details sd
                JOIN sale_summary ss ON ss.id = sd.sale_summary_id
                GROUP BY sd.product_id
            ) ss ON ss.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sai.product_id,
                    SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END) AS adjustment_addition,
                    SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END) AS adjustment_subtraction
                FROM stock_adjustment_items sai
                JOIN stock_adjustments sa ON sa.id = sai.adjustment_id
                GROUP BY sai.product_id
            ) sa ON sa.product_id = p.id

            WHERE sdt.sale_summary_id = $sale_id 
              AND sdt.created_by = '" . auth()->id() . "'
            ORDER BY sdt.id DESC
        ");
            $data = collect($data);
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

    public function pos_getSaleView($sale_id)
    {
        try {
            $data = DB::select("
            SELECT 
                sdt.id,
                sdt.product_id,
                sdt.quantity,
                sdt.selling_unit_price,
                sdt.subtotal,
                sdt.customer_id,
                p.name AS productName,
                p.barcode AS productBarcode,
                p.product_image AS productImg,
                
                (
                    COALESCE(ps.purchased_qty, 0)
                    - COALESCE(ps.returned_qty, 0)
                    - COALESCE(ss.sold_qty, 0)
                    + COALESCE(ss.sale_return_qty, 0)
                    + COALESCE(sa.adjustment_addition, 0)
                    - COALESCE(sa.adjustment_subtraction, 0)
                ) AS stock

            FROM pos_sale_details_temp sdt
            JOIN products p ON sdt.product_id = p.id

            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    SUM(CASE WHEN pu.document_type = 'P' THEN pi.quantity ELSE 0 END) AS purchased_qty,
                    SUM(CASE WHEN pu.document_type = 'PR' THEN pi.quantity ELSE 0 END) AS returned_qty
                FROM purchase_items pi
                JOIN purchases pu ON pu.id = pi.purchase_id
                GROUP BY pi.product_id
            ) ps ON ps.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sd.product_id,
                    SUM(CASE WHEN ss.document_type IN ('S','PS') THEN sd.quantity ELSE 0 END) AS sold_qty,
                    SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END) AS sale_return_qty
                FROM sale_details sd
                JOIN sale_summary ss ON ss.id = sd.sale_summary_id
                GROUP BY sd.product_id
            ) ss ON ss.product_id = p.id

            LEFT JOIN (
                SELECT 
                    sai.product_id,
                    SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END) AS adjustment_addition,
                    SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END) AS adjustment_subtraction
                FROM stock_adjustment_items sai
                JOIN stock_adjustments sa ON sa.id = sai.adjustment_id
                GROUP BY sai.product_id
            ) sa ON sa.product_id = p.id

            WHERE sdt.sale_summary_id = $sale_id 
              AND sdt.created_by = '" . auth()->id() . "'
            ORDER BY sdt.id DESC
        ");
            $data = collect($data);
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
        // dd($request->all());
        $reference = $request->reference;
        $prefix = explode("_", $reference)[0];
        
        DB::beginTransaction();

        try {
            while (DB::table('sale_summary')->where('invoice_number', $reference)->exists()) {
                $reference = $prefix . '_' . rand(1000, 999999);
            }
            $request->merge(['reference' => $reference]);
            // Validate input
            $validator = Validator::make($request->all(), [
                'sale_date' => 'required|date',
                'customer_id_hidden' => 'required|integer|exists:customers,id',
                //'customer_name' => 'required|string|max:100',
                'reference' => 'required|string|unique:sale_summary,invoice_number',
                //'customer_type' => 'required|in:cash,credit',
                'order_tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'shipping' => 'nullable|numeric',
                'status' => 'required',
                'note' => 'nullable|string',
                // 👇 yahan custom closure rule add kar rahe hain
                // 'sale_id' => [
                //     'required',
                //     function ($attribute, $value, $fail) {
                //         $exists = DB::table('sale_details_temp')
                //             ->where('sale_summary_id', $value)
                //             ->where('created_by', auth()->id())
                //             ->exists();

                //         if (!$exists) {
                //             $fail('Product not found.');
                //         }
                //     },
                // ],
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            if ($prefix == 'PS') {
                $tempItems = DB::table('pos_sale_details_temp')
                    ->where('sale_summary_id', $request->sale_id)
                    ->where('created_by', auth()->id())
                    ->get();
                // dd($tempItems);
            } else {
                $tempItems = DB::table('sale_details_temp')
                    ->where('sale_summary_id', $request->sale_id)
                    ->where('created_by', auth()->id())
                    ->get();
            }


            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary sale table.'], 400);
            }

            // Calculate total from temp items
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $taxPercent = $request->order_tax ?? 0;
            $taxCalc = ($totalAmount * $taxPercent) / 100;
            $shipping = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

            // Insert into sale_summary
            $saleId = DB::table('sale_summary')->insertGetId([
                'created_by' => auth()->id(),
                'store_id' => $request->store_id ?? null,
                'customer_id' => $request->customer_id_hidden,
                'document_type' => ($prefix == 'PS') ? "PS" : "S",
                'customer_type' => "cash",
                'invoice_number' => $request->reference,
                'customer_name' => $request->customer_name,
                'sale_date' => ($request->sale_date) ? $request->sale_date : now()->format('Y-m-d'),
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $taxPercent,
                'shipping_charge' => $shipping,
                'extra_amount' => $request->extra_amount,
                'grand_total' => $grandTotal,
                'notes' => $request->note,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert items into sale_details
            foreach ($tempItems as $item) {
                DB::table('sale_details')->insert([
                    'sale_summary_id' => $saleId,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $item->warehouse_id,
                    'quantity' => $item->quantity,
                    'cost_unit_price' => $item->cost_unit_price,
                    'selling_unit_price' => $item->selling_unit_price,
                    'subtotal' => $item->subtotal,
                    'sale_date' => $request->sale_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $invoiceNumber = DB::table('sale_summary')
                ->where('id', $saleId)
                ->value('invoice_number');
            if ($prefix == 'PS') {
                DB::table('pos_sale_details_temp')
                    ->where('sale_summary_id', $request->sale_id)
                    ->where('created_by', auth()->id())
                    ->delete();
            } else {
                DB::table('sale_details_temp')
                    ->where('sale_summary_id', $request->sale_id)
                    ->where('created_by', auth()->id())
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => 'Sale successfully saved.',
                'sale_id' => $saleId,
                'invoice_number' => $invoiceNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function posStoreFinalSaleDraft(Request $request)
    {
        DB::beginTransaction();
        $reference = $request->reference;
        $prefix = explode("_", $reference)[0];
        try {
            while (DB::table('sale_summary')->where('invoice_number', $reference)->exists()) {
                $reference = $prefix . '_' . rand(1000, 999999);
            }
            $request->merge(['reference' => $reference]);
    
            // Validate input
            $validator = Validator::make($request->all(), [
                'customer_id_hidden' => 'required|integer|exists:customers,id',
                'reference' => 'required|string|unique:pos_draft_sale_summary,invoice_number',
                'order_tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'shipping' => 'nullable|numeric',
                'status' => 'required',
                'note' => 'nullable|string',
                // 'sale_id' => [
                //     'required',
                //     function ($attribute, $value, $fail) {
                //         $exists = DB::table('sale_details_temp')
                //             ->where('sale_summary_id', $value)
                //             ->where('created_by', auth()->id())
                //             ->exists();

                //         if (!$exists) {
                //             $fail('Product not found.');
                //         }
                //     },
                // ],
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            // dd($request->sale_id);
            // Fetch sale items from temporary table (user/session wise)
            $tempItems = DB::table('pos_sale_details_temp')
                ->where('sale_summary_id', $request->sale_id)
                ->where('created_by', auth()->id())
                ->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary sale table.'], 400);
            }

            // Calculate total from temp items
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $taxPercent = $request->order_tax ?? 0;
            $taxCalc = ($totalAmount * $taxPercent) / 100;
            $shipping = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

            // Insert into pos_draft_sale_summary
            $saleId = DB::table('pos_draft_sale_summary')->insertGetId([
                'created_by' => auth()->id(),
                'store_id' => $request->store_id ?? null,
                'customer_id' => $request->customer_id_hidden,
                'document_type' => ($prefix == 'PS') ? "PS" : "S",
                'customer_type' => "cash",
                'invoice_number' => $request->reference,
                'customer_name' => $request->customer_name,
                'sale_date' => ($request->sale_date) ? $request->sale_date : now()->format('Y-m-d'),
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $taxPercent,
                'shipping_charge' => $shipping,
                'grand_total' => $grandTotal,
                'notes' => $request->note,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert items into sale_details
            foreach ($tempItems as $item) {
                DB::table('pos_draft_sale_details')->insert([
                    'pos_draft_sale_summary_id' => $saleId,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $item->warehouse_id,
                    'quantity' => $item->quantity,
                    'cost_unit_price' => $item->cost_unit_price,
                    'selling_unit_price' => $item->selling_unit_price,
                    'subtotal' => $item->subtotal,
                    'sale_date' => $request->sale_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clear temp table (current user/session only)
            DB::table('pos_sale_details_temp')
                ->where('sale_summary_id', $request->sale_id)
                ->where('created_by', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'success' => 'Sale draft save.',
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

    // ✅ Last 3 Sales (Product mandatory, customer optional)
    public function getLastSales(Request $request, $productId)
    {
        $customerId = $request->customer_id;
        $query = DB::table('sale_details as sd')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->join('customers as c', 'ss.customer_id', '=', 'c.id')
            ->select(
                'ss.id as sale_id',
                'sd.product_id',
                'p.name as product_name',
                'sd.quantity',
                'sd.selling_unit_price as sale_price',
                'ss.customer_id',
                'ss.sale_date as sale_date',
                'c.name as customer_name',
            )
            ->orderBy('ss.sale_date', 'desc')
            ->limit(3)
            ->where('ss.document_type', 'S')
            // ->whereIn('ss.document_type', ['S', 'PS'])
            ->where('sd.product_id', $productId);
        // if (!empty($customerId)) {
        //     dd('not empty');
        $query->where('ss.customer_id', $customerId);
        // }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Last 3 Sales fetched successfully',
            'data' => $data->toArray()
        ]);
    }

    public function saleEdit($id)
    {
        $sale = DB::table('sale_summary')
            ->where('id', $id)
            ->first();
        // Step 1: Check agar temp table me already data hai
        $checkTemp = DB::table('sale_details_temp')
            ->where('sale_summary_id', $id)
            ->where('created_by', auth()->id())
            ->count();
        if ($checkTemp <= 0) {
            // Step 2: Sale details se items lena
            $items = DB::table('sale_details')
                ->where('sale_summary_id', $id)
                ->get();

            // Step 4: Sale summary table se main record fetch karna
            // $sale = DB::table('sale_summary')
            // ->where('id', $id)
            // ->first();


            // Step 3: Temp table me copy karna
            foreach ($items as $item) {
                DB::table('sale_details_temp')->insert([
                    'sale_summary_id' => $item->sale_summary_id,
                    'product_id' => $item->product_id,
                    //'variant_id'      => $item->variant_id,
                    'warehouse_id' => $item->warehouse_id,
                    'customer_id' => $sale->customer_id,
                    'quantity' => $item->quantity,
                    'cost_unit_price' => $item->cost_unit_price,
                    'selling_unit_price' => $item->selling_unit_price,
                    //'discount'        => $item->discount,
                    //'tax'             => $item->tax,
                    'subtotal' => $item->subtotal,
                    'edit_stock' => $item->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'created_by' => auth()->id(), // user track karne ke liye
                ]);
            }
        }


        // Step 5: View return karna
        return view('admin.sale.edit', compact('id', 'sale'));
    }


    public function storeFinalSaleEdit(Request $request)
    {
        DB::beginTransaction();
        $id = $request->sale_id;

        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'sale_id' => 'required|numeric',
                'sale_date' => 'required|date',
                'customer_id_hidden' => 'required|integer|exists:customers,id',
                'reference' => 'required|string|unique:sale_summary,invoice_number,' . $id, // apne record ko ignore karo
                'order_tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'shipping' => 'nullable|numeric',
                'status' => 'required|in:completed,pending,canceled,ordered,received',
                'note' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch sale items from temp table
            $tempItems = DB::table('sale_details_temp')
                ->where('sale_summary_id', $id)
                ->where('created_by', auth()->id())
                ->get();


            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary sale table.'], 400);
            }

            // Calculate total
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $tax = $request->order_tax ?? 0;
            $taxCalc = ($totalAmount * ($request->order_tax ?? 0)) / 100;
            $shipping = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

            // Update sale_summary table
            DB::table('sale_summary')->where('id', $id)->update([
                'customer_id' => $request->customer_id_hidden,
                'invoice_number' => $request->reference,
                'sale_date' => $request->sale_date,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_charge' => $shipping,
                'grand_total' => $grandTotal,
                'notes' => $request->note,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            // Purane sale details delete karo
            DB::table('sale_details')->where('sale_summary_id', $id)->delete();

            // Naye items insert karo
            foreach ($tempItems as $item) {
                DB::table('sale_details')->insert([
                    'sale_summary_id' => $id,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $item->warehouse_id,
                    'quantity' => $item->quantity,
                    'cost_unit_price' => $item->cost_unit_price,
                    'selling_unit_price' => $item->selling_unit_price,
                    'subtotal' => $item->subtotal,
                    'sale_date' => $request->sale_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clear temp table
            DB::table('sale_details_temp')->where('sale_summary_id', $id)
                ->where('created_by', auth()->id())
                ->delete();


            DB::commit();

            return response()->json(['success' => 'Sale updated successfully.', 'sale_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saleDelete($id)
    {
        DB::beginTransaction();
        try {
            // Delete all sale details first
            DB::table('sale_details')->where('sale_summary_id', $id)->delete();

            // Then delete main sale summary
            $deleted = DB::table('sale_summary')->where('id', $id)->delete();

            if ($deleted) {
                DB::commit();
                return response()->json(['success' => 'Sale deleted successfully'], 200);
            } else {
                DB::rollBack();
                return response()->json(['error' => 'Sale not found.'], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saleTempDelete($id)
    {
        try {
            // Delete all sale items from temp table
            $deleted = DB::table('sale_details_temp')
                ->where('sale_summary_id', $id)
                ->where('created_by', auth()->id())
                ->delete();

            if ($deleted) {
                return response()->json(['success' => 'Sale Temp deleted successfully'], 200);
            } else {
                return response()->json(['success' => 'Sale Temp deleted successfully'], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getInvoiceData($sale_id)
    {
        $sale = DB::table('sale_summary')
            ->join('customers as customer', 'sale_summary.customer_id', '=', 'customer.id')
            ->select(
                'sale_summary.*',
                'customer.name as customer_name',
                'customer.email as customer_email',
                'customer.phone as customer_phone',
                'customer.address as customer_address',
                'customer.country as customer_country',
                'customer.city as customer_city',
                'customer.tax_number as customer_tax_number',
                'customer.owner as customer_status'
            )
            ->where('sale_summary.id', $sale_id)
            ->first();

        $sale_items = DB::table('sale_details')
            ->join('products as product', 'sale_details.product_id', '=', 'product.id')
            ->join('units as product_unit', 'product.unit_id', '=', 'product_unit.id')
            ->select(
                'sale_details.*',
                'product.name as product_name',
                'product.barcode as product_barcode',
                'product.unit_id',
                'product_unit.name as unit_name'
            )
            ->where('sale_details.sale_summary_id', $sale_id) // Explicit table reference
            ->get();

        return [
            'sale' => $sale,
            'items' => $sale_items
        ];
    }

    public function sale_view($sale_id)
    {
        $result = $this->getInvoiceData($sale_id);
        return view('admin.sale.view', compact('result'));
    }

    public function sale_download($sale_id)
    {
        $result = $this->getInvoiceData($sale_id);
        $pdf = Pdf::loadView('admin.sale.view_pdf', compact('result'));
        // view pdf view open below commit 
        // return view('admin.sale.view_pdf', compact('result'));

        return $pdf->download('sale-' . $result['sale']->invoice_number . '.pdf');
    }

    public function sale_with_profit_download($sale_id)
    {
        $result = $this->getInvoiceData($sale_id);
        $pdf = Pdf::loadView('admin.sale.view_with_profit_pdf', compact('result'));
        // view pdf view open below commit 
        // return view('admin.sale.view_with_profit_pdf', compact('result'));

        return $pdf->download('sale-' . $result['sale']->invoice_number . '.pdf');
    }

    public function deleteAll(Request $request)
    {
        DB::table('sale_details_temp')->where('sale_summary_id', $request->sale_id)->delete();
        return response()->json(['success' => 'Reset Sale successfully'], 200);
    }
    public function posDeleteAll(Request $request)
    {
        DB::table('pos_sale_details_temp')->where('sale_summary_id', $request->sale_id)->where('created_by', auth()->user()->id)->delete();
        return response()->json(['success' => 'Reset Sale successfully'], 200);
    }
    public function pos_draft_list()
    {
        $posDraftSummery = DB::table('pos_draft_sale_summary as pds')
            ->join('customers as c', 'pds.customer_id', '=', 'c.id')
            ->where('pds.created_by', auth()->user()->id)
            ->select('pds.*', 'c.name as customer_name')
            ->get();


        return response()->json([
            'success' => 'pos draft summery list.',
            'posDraftSummery' => $posDraftSummery
        ]);
    }
    public function posTodaySaleSummery()
    {
        $total = DB::table('sale_summary')
            ->where('document_type', 'PS')
            ->whereDate('created_at', Carbon::today())
            ->sum('grand_total');

        return response()->json([
            'date' => Carbon::today()->toDateString(),
            'today_sale' => $total,
            'currency_symbol' => env('CURRENCY_SYMBLE')
        ]);
    }
    public function posDraftSaleDetail($id)
    {
        DB::table('pos_sale_details_temp')
            ->where('created_by', auth()->id())
            ->delete();
        $draftSummery = DB::table('pos_draft_sale_summary')
            ->where('invoice_number', $id)
            ->first();

        if (!$draftSummery) {
            return response()->json(['error' => 'Draft sale not found'], 404);
        }
        $draft_sale_details = DB::table('pos_draft_sale_details')
            ->where('pos_draft_sale_summary_id', $draftSummery->id)
            ->get();

        foreach ($draft_sale_details as $detail) {
            $subtotal = $detail->quantity * $detail->selling_unit_price;

            DB::table('pos_sale_details_temp')->insert([
                'sale_summary_id' => 999, // 🔄 replace with real sale_summary_id
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'cost_unit_price' => $detail->cost_unit_price,
                'selling_unit_price' => $detail->selling_unit_price,
                'subtotal' => $subtotal,
                'sale_date' => $detail->sale_date,
                'customer_id' => $detail->customer_id,
                'warehouse_id' => null,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        DB::table('pos_draft_sale_details')
            ->where('pos_draft_sale_summary_id', $draftSummery->id)
            ->delete();

        DB::table('pos_draft_sale_summary')
            ->where('id', $draftSummery->id)
            ->delete();

        return response()->json([
            'success' => 'Draft converted to sale!',
            'invoice_number' => $id,
            'customer_id' => $draftSummery->customer_id,
        ]);
    }
}
