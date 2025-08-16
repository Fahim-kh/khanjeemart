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
        DB::table('purchase_items_temp')->where('purchase_id', '!=', 999)->delete();
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
            ->orderBy('purchases.id', 'desc')
            ->get(); 
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
       DB::table('purchase_items_temp')->delete();
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
                'product.unit_id',
                'product_unit.name as unit_name'
            )
            ->where('purchase_id', $purchase_id)
            ->get();

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



    //purchase return work

    public function purchaseReturn($id)
    {
        return view('admin.purchase.return', compact('id'));
    }


    public function purchaseReturnItems($purchase_id)
    {
        $items = DB::table('purchase_items as pi')
                ->join('products', 'pi.product_id', '=', 'products.id')
                ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
                ->leftJoin('purchase_items as pri', function ($join) {
                    $join->on('pri.product_id', '=', 'pi.product_id')
                        ->join('purchases as pr', 'pri.purchase_id', '=', 'pr.id')
                        ->where('pr.document_type', '=', 'PR')
                        ->whereColumn('pr.ref_document_no', 'p.id');
                })
                ->where('pi.purchase_id', $purchase_id)
                ->where('p.document_type', '=', 'P') // Original purchase only
                ->select(
                    'pi.product_id',
                    'products.barcode',
                    'products.name as product_name',
                    'pi.unit_cost',
                    'pi.quantity as qty_purchased',
                    DB::raw('COALESCE(SUM(pri.quantity), 0) as qty_returned'),
                    'pi.discount',
                    'pi.tax',
                    'pi.subtotal',
                    DB::raw('(pi.quantity - COALESCE(SUM(pri.quantity), 0)) as stock_qty')
                )
                ->groupBy(
                    'pi.product_id',
                    'products.barcode',
                    'products.name',
                    'pi.unit_cost',
                    'pi.quantity',
                    'pi.discount',
                    'pi.tax',
                    'pi.subtotal'
                )
                ->get();

$itemsWithCounter = $items->map(function ($item, $index) {
    return [
        'row_no'        => $index + 1,
        'product_id'    => $item->product_id,
        'barcode'       => $item->barcode,
        'product_name'  => $item->product_name,
        'net_unit_cost' => number_format($item->unit_cost, 2),
        'qty_purchased' => $item->qty_purchased,
        'stock_qty'     => $item->stock_qty,
        'discount'      => number_format($item->discount, 2),
        'tax'           => number_format($item->tax, 2),
        'subtotal'      => number_format($item->subtotal, 2),
    ];
});


       

        return response()->json($itemsWithCounter);
    }


    public function purchaseReturnStore(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'reference' => 'required|string',
            'purchase_id' => 'required|integer',
            'status' => 'required|string',
            'order_tax' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'qty_return' => 'required|array'
        ]);

        if (!$validator->passes()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }


        DB::beginTransaction();

        try {
            $grandTotal = 0;
            $getPurchase = PurchaseModel::findOrFail($request->purchase_id);
            // 1. New purchase record for Return
            $returnPurchase = PurchaseModel::create([
                'created_by'      => auth()->id(),
                'store_id'        => $request->store_id ?? null,
                'supplier_id'     => $getPurchase->supplier_id,
                'document_type'   => 'PR',
                'invoice_number'  => $request->reference,
                'purchase_date'   => $request->date,
                'total_amount'    => 0, // calculate later
                'discount'        => $request->discount ?? 0,
                'tax'             => $request->order_tax ?? 0,
                'shipping_charge' => $request->shipping ?? 0,
                'grand_total'     => 0, // calculate later
                'notes'           => $request->note ?? '',
                'ref_document_no' => $request->purchase_id, // original purchase id
                'status'          => $request->status
            ]);

            // 2. Save returned items
            foreach ($request->qty_return as $index => $qty) {
                if ($qty > 0) {
                    $purchaseItem = PurchaseItems::where('purchase_id', $request->purchase_id)
                        ->skip($index)->take(1)->first();

                    if ($purchaseItem) {
                        $subtotal = $purchaseItem->unit_cost * $qty;
                        $grandTotal += $subtotal;

                        PurchaseItems::create([
                            'purchase_id'  => $returnPurchase->id,
                            'product_id'   => $purchaseItem->product_id,
                            'variant_id'   => $purchaseItem->variant_id,
                            'warehouse_id' => $purchaseItem->warehouse_id,
                            'quantity'     => $qty,
                            'unit_cost'    => $purchaseItem->unit_cost,
                            'sale_price'   => $purchaseItem->sale_price,
                            'discount'     => $purchaseItem->discount,
                            'tax'          => $purchaseItem->tax,
                            'subtotal'     => $subtotal
                        ]);
                    }
                }
            }

            $subTotal = $grandTotal; // total of returned items
            $taxAmount = ($request->order_tax ?? 0) > 0 
                ? ($subTotal * ($request->order_tax / 100)) 
                : 0;

            $grandTotal = $subTotal + $taxAmount + ($request->shipping ?? 0) - ($request->discount ?? 0);

            $returnPurchase->update([
                'total_amount' => $subTotal,
                'tax'          => $taxAmount,
                'grand_total'  => $grandTotal
            ]);

            DB::commit();

            return response()->json(['success' => 'Purchase successfully saved.', 'purchase_id' => $request->purchase_id]);
            //return redirect()->route('purchase.index')->with('success', 'Purchase return saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
             return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
            //return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


}
