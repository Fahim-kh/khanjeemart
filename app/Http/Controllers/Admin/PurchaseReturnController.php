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
class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.purchase_return.index');
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      
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
            ->where('purchases.document_type', 'PR')
            ->orderBy('purchases.id', 'desc')
            ->get(); 
            //dd($purchases);  
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_delete_display_button($data->id, 'purchase_return', 'Purchase Return');
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



    //purchase return work
    public function purchaseReturn($id)
    {
        return view('admin.purchase_return.return', compact('id'));
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
                $productId = $request->product_id[$index]; // ✅ same index ka product_id lo

                if ($qty > 0) {
                    $purchaseItem = PurchaseItems::where('purchase_id', $request->purchase_id)
                        ->where('product_id', $productId) // ✅ yaha product_id se match karo
                        ->first();

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
                //'tax'          => $taxAmount,
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

    private function getPurchaseReturnData($return_id)
    {
        // Main purchase return record
        $purchaseReturn = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->leftJoin('purchases as original_purchase', 'purchases.ref_document_no', '=', 'original_purchase.id')
            ->select(
                'purchases.*',
                'suppliers.name as supplier_name',
                'original_purchase.invoice_number as org_purchase_invoice'
            )
            ->where('purchases.id', $return_id)
            ->where('purchases.document_type', 'PR')
            ->first();

        if (!$purchaseReturn) {
            return [
                'success' => false,
                'message' => 'Purchase Return not found'
            ];
        }

        // Return items
        $returnItems = DB::table('purchase_items')
            ->join('products as product', 'purchase_items.product_id', '=', 'product.id')
            ->join('units as product_unit', 'product.unit_id', '=', 'product_unit.id')
            ->select(
                'purchase_items.*',
                'product.name as product_name',
                'product.id as product_code',
                'product_unit.name as unit_name'
            )
            ->where('purchase_items.purchase_id', $return_id)
            ->get();

        return [
            'success' => true,
            'return' => $purchaseReturn,
            'items'  => $returnItems
        ];
    }


    public function viewDetail($id)
    {
        $result = $this->getPurchaseReturnData($id);
        return response()->json($result);
    }


}
