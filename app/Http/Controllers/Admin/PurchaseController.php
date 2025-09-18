<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseModel;
use App\Models\Supplier;
use App\Models\ProductModel;
use App\Models\PurchaseItems;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function purchaseEdit($id)
    {
        $checkTemp = DB::table('purchase_items_temp')
            ->where('purchase_id', $id)
            ->count();

        if ($checkTemp <= 0) {
            $items = DB::table('purchase_items')
                ->where('purchase_id', $id)
                ->get();

            foreach ($items as $item) {
                DB::table('purchase_items_temp')->insert([
                    'purchase_id' => $item->purchase_id,
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
        }
        // Step 2: Purchase table se required columns lena
        $purchase = DB::table('purchases')
            //->select('invoice_number', 'purchase_date', 'supplier_id')
            ->where('id', $id)
            ->first();

        // Step 3: View return karna
        return view('admin.purchase.edit', compact('id', 'purchase'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //DB::table('purchase_items_temp')->where('purchase_id', '!=', 999)->delete();
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
            ->where('purchases.document_type', 'P')
            ->orderBy('purchases.id', 'desc');
            //->get(); 
            //dd($purchases);  
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_action_dropdown($data->id, 'purchase', 'Purchase');
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
            $data = DB::table('purchase_items_temp')
            ->join('products', 'products.id', '=', 'purchase_items_temp.product_id')
            ->where('purchase_items_temp.id', $id)
            ->select(
                'purchase_items_temp.*',
                'products.name as product_name'
            )
            ->first();
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
                function ($attribute, $value, $fail) use ($request) {
                    //if (!empty($request->purchase_id)) {
                        $exists = DB::table('purchase_items_temp')
                            ->where('product_id', $value)
                            ->where('purchase_id', $request->purchase_id)
                            ->exists();

                        if ($exists) {
                            $fail('This product already exists for this purchase.');
                        }
                   // }
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
                'purchase_id' => $request->purchase_id ?? 999,
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


            $stock = $this->getProductStock($id);

            return response()->json([
                'success' => 'success',
                'product_id' => $id,
                'name' => $product->name,
                'bar_code' => $product->barcode,
                'average_unit_cost' => round($avgUnitCost, 2),
                'last_sale_price' => round($lastSalePrice, 2),
                'stock' => $stock
            ]);
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }


    function getProductStock($productId)
    {
        // Purchase (final)
        $purchase = DB::table('purchase_items as pi')
            ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
            ->where('pi.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN p.document_type = 'P' THEN pi.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN p.document_type = 'PR' THEN pi.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $purchaseQty = $purchase ? $purchase->total : 0;

        // Purchase (temp)
        // $purchaseTempQty = DB::table('purchase_items_temp')
        //     ->where('product_id', $productId)
        //     ->sum('quantity');

        // Sale (final)
        $sale = DB::table('sale_details as sd')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->where('sd.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN ss.document_type = 'S' THEN sd.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $saleQty = $sale ? $sale->total : 0;

        // Sale (temp)
        // $saleTempQty = DB::table('sale_details_temp')
        //     ->where('product_id', $productId)
        //     ->sum('quantity');


        // Stock Adjustment
        $adjustment = DB::table('stock_adjustment_items as sai')
            ->join('stock_adjustments as sa', 'sai.adjustment_id', '=', 'sa.id')
            ->where('sai.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $adjustmentQty = $adjustment ? $adjustment->total : 0;    


        // Final Stock
        //$stock = ($purchaseQty) - ($saleQty + $saleTempQty);
        $stock = ($purchaseQty + $adjustmentQty) - ($saleQty);

        return $stock;
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
            $tempItems = DB::table('purchase_items_temp')->where('purchase_id', $request->purchase_id)->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary purchase table.'], 400);
            }

            // Calculate total from temp items
            $totalAmount = $tempItems->sum('subtotal');

            // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $tax = $request->order_tax ?? 0;
            $taxCalc = ($totalAmount * ($request->order_tax ?? 0)) / 100; 
            $shipping = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

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
            DB::table('purchase_items_temp')->where('purchase_id', $request->purchase_id)->delete();

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



    public function storeFinalPurchaseEdit(Request $request)
    {
        DB::beginTransaction();
        $id = $request->purchase_id;
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'purchase_id' => 'required|numeric',
                'date' => 'required|date',
                'supplier_id' => 'required|integer|exists:suppliers,id', 
                'reference' => 'required|string|unique:purchases,invoice_number,' . $id, // apne record ko ignore karo
                'order_tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'shipping' => 'nullable|numeric',
                'status' => 'required|in:received,pending,canceled,ordered',
                'note' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            // Fetch purchase items from temp table
            $tempItems = DB::table('purchase_items_temp')->where('purchase_id', $id)->get();

            if ($tempItems->isEmpty()) {
                return response()->json(['error' => 'No items found in temporary purchase table.'], 400);
            }

            // Calculate total
            $totalAmount = $tempItems->sum('subtotal');

             // Apply discount, tax, shipping
            $discount = $request->discount ?? 0;
            $tax = $request->order_tax ?? 0;
            $taxCalc = ($totalAmount * ($request->order_tax ?? 0)) / 100; 
            $shipping = $request->shipping ?? 0;
            $grandTotal = $totalAmount - $discount + $taxCalc + $shipping;

            

            // Update purchases table
            DB::table('purchases')->where('id', $id)->update([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->reference,
                'purchase_date' => $request->date,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_charge' => $shipping,
                'grand_total' => $grandTotal,
                'notes' => $request->note,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            // Purane purchase items delete karo
            DB::table('purchase_items')->where('purchase_id', $id)->delete();

            // Naye items insert karo
            foreach ($tempItems as $item) {
                DB::table('purchase_items')->insert([
                    'purchase_id' => $id,
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
            DB::table('purchase_items_temp')->where('purchase_id', $id)->delete();

            DB::commit();

            return response()->json(['success' => 'Purchase updated successfully.', 'purchase_id' => $id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }




    public function getPurchaseView($purchase_id)
    {
        try {
            $data = DB::table('purchase_items_temp')
            ->join('products as product', 'purchase_items_temp.product_id', '=', 'product.id')
            ->select(
                'purchase_items_temp.*',
                'product.name as productName',
                'product.barcode as bar_code',
                'product.product_image as productImg'
            )
            ->where('purchase_id',$purchase_id)
            ->orderBy('purchase_items_temp.id','DESC')
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
                //'supplier_id' => 'required|integer|exists:suppliers,id',
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
                //'purchase_bill_date' => $request->date,
                'product_id'         => $request->product_id,
                'quantity'           => $request->quantity,
                'unit_cost'          => $request->unit_cost,
                'sale_price'          => $request->sell_price,
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
       DB::table('purchase_items_temp')->where('purchase_id', $request->purchase_id)->delete();
       return response()->json(['success' => 'Purchase updated successfully'], 200);
    }

   
    public function pdelete($id)
    {
        DB::beginTransaction();
        try {
            // Delete all purchase items first
            DB::table('purchase_items')->where('purchase_id', $id)->delete();

            // Then delete main purchase
            $deleted = DB::table('purchases')->where('id', $id)->delete();

            if ($deleted) {
                DB::commit();
                return response()->json(['success' => 'Purchase deleted successfully'], 200);
            } else {
                DB::rollBack();
                return response()->json(['error' => 'Purchase not found.'], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pTempDelete($id)
    {
        try {
            // Delete all purchase items first
            $deleted = DB::table('purchase_items_temp')->where('purchase_id', $id)->delete();

            if ($deleted) {
                return response()->json(['success' => 'Purchase  Temp deleted successfully'], 200);
            } else {
                return response()->json(['success' => 'Purchase  Temp deleted successfully'], 200);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    private function getInvoiceData($purchase_id)
    {
        $purchase = DB::table('purchases')
            ->join('suppliers as supplier', 'purchases.supplier_id', '=', 'supplier.id')
            ->select(
                'purchases.*',
                'supplier.name as supplier_name',
                'supplier.email as supplier_email',
                'supplier.phone as supplier_phone',
                'supplier.address as supplier_address',
                'supplier.country as supplier_country',
                'supplier.city as supplier_city',
                'supplier.tax_number as supplier_tax_number'
            )
            ->where('purchases.id', $purchase_id)
            ->first();
        
        $purchase_items = DB::table('purchase_items')
            ->join('products as product', 'purchase_items.product_id', '=', 'product.id')
            ->join('units as product_unit', 'product.unit_id', '=', 'product_unit.id')
            ->select(
                'purchase_items.*',
                'product.name as product_name',
                'product.barcode as product_barcode',
                'product.unit_id',
                'product_unit.name as unit_name'
            )
            ->where('purchase_items.purchase_id', $purchase_id) // Explicit table reference
            ->get();
        // dd($purchase_items);
        return [
            'purchase' => $purchase,
            'items' => $purchase_items
        ];
    }

    public function purchase_view($purchase_id)
    {
        $result = $this->getInvoiceData($purchase_id);
        return view('admin.purchase.view', compact('result'));
    }

    public function purchase_download($purchase_id)
    {
        $result = $this->getInvoiceData($purchase_id);
        $pdf = Pdf::loadView('admin.purchase.view_pdf', compact('result'));
        return $pdf->download('purchase-'.$result['purchase']->invoice_number.'.pdf');
    }

   // ✅ Last 3 Purchases
    public function getLastPurchases($productId)
    {
        $data = DB::table('purchase_items as pi')
            ->join('products as p', 'pi.product_id', '=', 'p.id')
            ->join('purchases as pu', 'pi.purchase_id', '=', 'pu.id')
            ->where('pu.document_type', 'P')
            ->where('pi.product_id', $productId)
            ->orderBy('pu.purchase_date', 'desc')
            ->limit(3)
            ->select(
                'pu.id as purchase_id',
                'pi.product_id',
                'p.name as product_name',
                'pi.quantity',
                'pi.unit_cost',
                'pu.purchase_date as purchase_date'
            )
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Last 3 Purchases fetched successfully',
            'data' => $data->toArray()
        ]);
    }


}
