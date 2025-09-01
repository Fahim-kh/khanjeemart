<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Validator;
use DataTables;
class StockAdjustmentController extends Controller
{
    public function index()
    {
        return view('admin.stock_adjustment.index');
    }

    public function create()
    {
        return view('admin.stock_adjustment.create');
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
            $deleted = DB::table('stock_adjustment_temps')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['success' => 'Sale item deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Record not found.'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function StoreStockAdjustment(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = DB::table('stock_adjustment_temps')
                            ->where('product_id', $value)
                            ->where('adjustment_id', $request->adjustment_id)
                            ->where('created_by', auth()->id())
                            ->exists();

                        if ($exists) {
                            $fail('This product is already exist in sale temp.');
                        }
                    },
                ],
                'quantity' => 'required|numeric',
                'unit_cost' => 'nullable|numeric|min:0',
                'adjustment_type' => 'required|in:addition,subtraction',
                'variant_id' => 'nullable|integer',
                'warehouse_id' => 'nullable|integer',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $subtotal = $request->quantity * ($request->unit_cost ?? 0);

            // Insert into stock_adjustment_temps
            DB::table('stock_adjustment_temps')->insert([
                'product_id'       => $request->product_id,
                'adjustment_id' => $request->adjustment_id,
                'variant_id'       => $request->variant_id,
                'warehouse_id'     => $request->warehouse_id,
                'quantity'         => $request->quantity,
                'unit_cost'        => $request->unit_cost,
                'subtotal'         => $subtotal,
                'adjustment_type'  => $request->adjustment_type, // ✅ addition / subtraction
                'created_by'       => auth()->id(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::commit();

            return response()->json(['success' => 'Product successfully added into stock_adjustment_temps.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function UpdateStockAdjustmentItem(Request $request)
    {
        try {
            $id = $request->id;

            // record nikalna
            $item = DB::table('stock_adjustment_temps')->where('id', $id)->first();
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Item not found'
                ]);
            }

            // nayi values (agar nahi bheji gayi to purani hi rakh lo)
            $quantity   = $request->has('quantity') ? (float) $request->quantity : $item->quantity;
            $unit_cost  = $request->has('unit_cost') ? (float) $request->unit_cost : $item->unit_cost;
            $type       = $request->has('adjustment_type') ? $request->adjustment_type : $item->adjustment_type;

            // subtotal calculation
            $subtotal = $quantity * $unit_cost;

            // update karna
            DB::table('stock_adjustment_temps')
                ->where('id', $id)
                ->update([
                    'quantity'        => $quantity,
                    'unit_cost'       => $unit_cost,
                    'subtotal'        => $subtotal,
                    'adjustment_type' => $type,
                    'updated_at'      => now(),
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



   public function getStockAdjustmentView($adjustment_id)
    {
        try {
            $data = DB::select("
                SELECT 
                    sat.id,
                    sat.product_id,
                    sat.adjustment_type,
                    sat.quantity,
                    sat.unit_cost,
                    sat.subtotal,
                    p.name AS productName,
                    p.product_image AS productImg,
                    
                    (
                        COALESCE(ps.purchased_qty, 0)
                        - COALESCE(ps.returned_qty, 0)
                        - COALESCE(ss.sold_qty, 0)
                        + COALESCE(ss.sale_return_qty, 0)
                        + COALESCE(sa.added_qty, 0)
                        - COALESCE(sa.removed_qty, 0)
                    ) AS stock

                FROM stock_adjustment_temps sat
                JOIN products p ON sat.product_id = p.id

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
                        SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END) AS sale_return_qty
                    FROM sale_details sd
                    JOIN sale_summary ss ON ss.id = sd.sale_summary_id
                    GROUP BY sd.product_id
                ) ss ON ss.product_id = p.id

                LEFT JOIN (
                    SELECT 
                        sai.product_id,
                        SUM(CASE WHEN sai.adjustment_type = 'plus' THEN sai.quantity ELSE 0 END) AS added_qty,
                        SUM(CASE WHEN sai.adjustment_type = 'minus' THEN sai.quantity ELSE 0 END) AS removed_qty
                    FROM stock_adjustment_items sai
                    GROUP BY sai.product_id
                ) sa ON sa.product_id = p.id

                WHERE sat.adjustment_id = $adjustment_id 
                AND sat.created_by = '".auth()->id()."'
            ");

            $data = collect($data);
            return response()->json([
                'success' => true,
                'data' => $data->toJson()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function storeFinalStockAdjustment(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'adjustment_date' => 'required|date',
                'reference'       => 'required|string|unique:stock_adjustments,reference',
                'store_id'        => 'nullable|integer',
                'note'            => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch adjustment items from temporary table (user wise)
            $tempItems = DB::table('stock_adjustment_temps')
                ->where('adjustment_id', $request->adjustment_id)
                ->where('created_by', auth()->id())
                ->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary stock adjustment table.'], 400);
            }

            // Calculate total amount (valuation ke liye)
            $totalAmount = $tempItems->sum('subtotal');

            // Insert into stock_adjustments (type column hata do kyunki item-wise hai)
            $adjustmentId = DB::table('stock_adjustments')->insertGetId([
                'created_by'      => auth()->id(),
                'store_id'        => $request->store_id,
                'adjustment_date' => $request->adjustment_date,
                'reference'       => $request->reference,
                'notes'           => $request->note,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Insert items into stock_adjustment_items
            foreach ($tempItems as $item) {
                DB::table('stock_adjustment_items')->insert([
                    'adjustment_id'   => $adjustmentId,
                    'product_id'      => $item->product_id,
                    'variant_id'      => $item->variant_id,
                    'warehouse_id'    => $item->warehouse_id,
                    'adjustment_type' => $item->adjustment_type, // ✅ item wise type save hoga
                    'quantity'        => $item->quantity,
                    'unit_cost'       => $item->unit_cost,
                    'subtotal'        => $item->subtotal,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // Clear temp table for this user
            DB::table('stock_adjustment_temps')
                ->where('adjustment_id', $request->adjustment_id)
                ->where('created_by', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'success'        => 'Stock adjustment successfully saved.',
                'adjustment_id'  => $adjustmentId,
                'total_amount'   => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    

   public function StockAdjustmentEdit($id)
    {
        // Step 1: Check agar temp table me already data hai
        $checkTemp = DB::table('stock_adjustment_temps')
            ->where('adjustment_id', $id)
            ->where('created_by', auth()->id())
            ->count();

        if ($checkTemp <= 0) {
            // Step 2: Stock adjustment items se data lena
            $items = DB::table('stock_adjustment_items')
                ->where('adjustment_id', $id)
                ->get();

            // Step 3: Temp table me copy karna
            foreach ($items as $item) {
                DB::table('stock_adjustment_temps')->insert([
                    'adjustment_id'    => $id,
                    'product_id'       => $item->product_id,
                    'variant_id'       => $item->variant_id,
                    'warehouse_id'     => $item->warehouse_id,
                    'quantity'         => $item->quantity,
                    'unit_cost'        => $item->unit_cost,
                    'subtotal'         => $item->subtotal,
                    'adjustment_type'  => $item->adjustment_type, // direct item ka type use karo
                    'created_at'       => now(),
                    'updated_at'       => now(),
                    'created_by'       => auth()->id(),
                ]);
            }
        }

        // Step 4: Stock adjustment table se main record fetch karna
        $adjustment = DB::table('stock_adjustments')
            ->where('id', $id)
            ->first();

        // Step 5: View return karna
        return view('admin.stock_adjustment.edit', compact('id', 'adjustment'));
    }



    public function storeFinalStockAdjustmentEdit(Request $request)
    {
        DB::beginTransaction();
        $id = $request->adjustment_id;

        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'adjustment_id'   => 'required|numeric',
                'adjustment_date' => 'required|date',
                'reference'       => 'required|string|unique:stock_adjustments,reference,' . $id,
                'note'            => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch adjustment items from temp table
            $tempItems = DB::table('stock_adjustment_temps')
                ->where('adjustment_id', $id)
                ->where('created_by', auth()->id())
                ->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary stock adjustment table.'], 400);
            }

            // Calculate total amount
            $totalAmount = $tempItems->sum('subtotal');

            // ✅ Master table update (without type)
            DB::table('stock_adjustments')->where('id', $id)->update([
                'adjustment_date' => $request->adjustment_date,
                'reference'       => $request->reference,
                'notes'           => $request->note,
                'updated_at'      => now(),
            ]);

            // Purane stock_adjustment_items delete karo
            DB::table('stock_adjustment_items')
                ->where('adjustment_id', $id)
                ->where('created_by', auth()->id())
                ->delete();

            // Naye items insert karo
            foreach ($tempItems as $item) {
                DB::table('stock_adjustment_items')->insert([
                    'adjustment_id'   => $id,
                    'product_id'      => $item->product_id,
                    'variant_id'      => $item->variant_id,
                    'warehouse_id'    => $item->warehouse_id,
                    'quantity'        => $item->quantity,
                    'unit_cost'       => $item->unit_cost,
                    'subtotal'        => $item->subtotal,
                    'adjustment_type' => $item->adjustment_type, // ✅ item wise save
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // Clear temp table
            DB::table('stock_adjustment_temps')
                ->where('adjustment_id', $id)
                ->where('created_by', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'success'        => 'Stock adjustment updated successfully.',
                'adjustment_id'  => $id
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
