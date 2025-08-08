<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseModel;
use App\Models\Supplier;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Validator;
use DataTables;
class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('status',1)->get();
        return view('admin.purchase.create',compact('suppliers'));
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
           $purchases = DB::table('purchases')
            ->select(
                'purchases.id',
                'purchases.purchase_date',
                'purchases.invoice_number',
                'purchases.grand_total',
                'purchases.status',
                'suppliers.name as supplier_name'
            )
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->orderBy('purchases.id', 'desc')
            ->get(); 
            //dd($purchases);  
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'purchases', 'Purchase');
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
            $data = DB::table('purchase_items_temp')->where('id', $id)->first();
            return response()->json(['success' => 'successfull retrieve data', 'data' => json_encode($data)], 200);
            
            
       
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
        $deleted = DB::table('purchase_items_temp')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => 'Supplier deleted successfully'], 200);
        } else {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function StorePurchase(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                //'supplier_id' => 'required|integer|exists:suppliers,id', 
                'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('purchase_items_temp')
                        ->where('product_id', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This product is already exist.');
                    }
                },
            ],
                'date' => 'required',
                'quantity' => 'required|numeric',
                'unit_cost' => 'required|numeric',
                'sell_price' => 'required|numeric'
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $sub_total = $request->quantity * $request->unit_cost;
            // Insert into purchase_items_temp
            DB::table('purchase_items_temp')->insert([
                'purchase_id' => 999, // or dynamically assign if needed
                //'supplier_id' => $request->supplier_id,
                //'purchase_bill_date' => $request->date,
                'product_id' => $request->product_id,
                //'product_name' => $request->product_name,
                'variant_id' => null,
                'warehouse_id' => null,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'sale_price' => $request->sell_price,
                'discount' => 0,
                'tax' => 0,
                'subtotal' => $sub_total, // subtotal calculation logic can be added
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json(['success' => 'Data successfully inserted into purchase_items_temp.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getAverageCostAndSalePrice(string $id)
    {
        try {
            
            $avgUnitCost = DB::table('purchase_items')
                ->where('product_id', $id)
                ->avg('unit_cost');

            $lastSalePrice = DB::table('purchase_items')
                ->where('product_id', $id)
                ->orderByDesc('id') // or 'created_at'
                ->value('sale_price');

            $product = ProductModel::find($id);

            return response()->json([
                'success' => 'success',
                'product_id' => $id,
                'name' => $product->name,
                'average_unit_cost' => round($avgUnitCost, 2),
                'last_sale_price' => round($lastSalePrice, 2),
            ]);
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function storeFinalPurchase(Request $request)
    {
        
        DB::beginTransaction();

        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'supplier_id' => 'required|integer|exists:suppliers,id', 
                'reference' => 'required|string|unique:purchases,invoice_number',
                'order_tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'shipping' => 'nullable|numeric',
                'status' => 'required|in:received,pending,canceled',
                'note' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch purchase items from temporary table
            $tempItems = DB::table('purchase_items_temp')->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary purchase table.'], 400);
            }

            // Calculate total from temp items
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $tax = $request->order_tax ?? 0;
            $shipping = $request->shipping ?? 0;

            $grandTotal = $totalAmount - $discount + $tax + $shipping;

            // Insert into purchases table
            $purchaseId = DB::table('purchases')->insertGetId([
                'created_by' => auth()->id(), // Or use default value
                'store_id' => null, // Update if store is applicable
                'supplier_id' => $request->supplier_id, // From temp data
                'invoice_number' => $request->reference,
                'purchase_date' => $request->date,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_charge' => $shipping,
                'grand_total' => $grandTotal,
                'notes' => $request->note,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert items into purchase_items
            foreach ($tempItems as $item) {
                DB::table('purchase_items')->insert([
                    'purchase_id' => $purchaseId,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'warehouse_id' => $item->warehouse_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'sale_price' => $item->sale_price,
                    'discount' => $item->discount,
                    'tax' => $item->tax,
                    'subtotal' => $item->subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clear temp table
            DB::table('purchase_items_temp')->delete();

            DB::commit();

            return response()->json(['success' => 'Purchase successfully saved.', 'purchase_id' => $purchaseId]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getPurchaseView()
    {
        try {
            $data = DB::table('purchase_items_temp')
                    ->join('products as product', 'purchase_items_temp.product_id', '=', 'product.id')
                    ->select(
                        'purchase_items_temp.*',
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

    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'          => 'required|integer|exists:purchase_items_temp,id',
                'supplier_id' => 'required|integer|exists:suppliers,id',
                'product_id'  => 'required|integer|exists:products,id',
                'date'        => 'required|date',
                'quantity'    => 'required|numeric',
                'unit_cost'   => 'required|numeric'
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $sub_total = $request->quantity * $request->unit_cost;

            // Update the record
            DB::table('purchase_items_temp')->where('id', $request->id)->update([
                'supplier_id'        => $request->supplier_id,
                'purchase_bill_date' => $request->date,
                'product_id'         => $request->product_id,
                'quantity'           => $request->quantity,
                'unit_cost'          => $request->unit_cost,
                'discount'           => 0,
                'tax'                => 0,
                'subtotal'           => $sub_total,
                'updated_at'         => now()
            ]);

             return response()->json(['success' => 'Purchase updated successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteAll(Request $request)
    {
       DB::table('purchase_items_temp')->delete();
       return response()->json(['success' => 'Purchase updated successfully'], 200);
    }

}
