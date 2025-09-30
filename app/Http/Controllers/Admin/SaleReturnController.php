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
class SaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.sale_return.index');
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
            $sales = DB::table('sale_summary')
                ->select(
                    'sale_summary.id',
                    'sale_summary.sale_date',
                    'sale_summary.invoice_number',
                    'sale_summary.grand_total',
                    'sale_summary.status',
                    'customers.name as customer_name'
                )
                ->join('customers', 'customers.id', '=', 'sale_summary.customer_id')
                ->where('sale_summary.document_type', 'SR') // Sale Return
                ->orderBy('sale_summary.id', 'desc');

            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return table_delete_display_button($data->id, 'sale_return', 'Sale Return');
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
            // Delete all sale items first
            DB::table('sale_details')->where('sale_summary_id', $id)->delete();

            // Then delete main sale return
            $deleted = DB::table('sale_summary')->where('id', $id)->delete();

            if ($deleted) {
                DB::commit();
                return response()->json(['success' => 'Sale Return deleted successfully'], 200);
            } else {
                DB::rollBack();
                return response()->json(['error' => 'Sale Return not found.'], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    //purchase return work
    public function saleReturn($id)
    {
        return view('admin.sale_return.return', compact('id'));
    }


    public function saleReturnItems($sale_id)
    {
        // dd($sale_id);
        $items = DB::table('sale_details as sd')
            ->join('products', 'sd.product_id', '=', 'products.id')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->leftJoin('sale_details as srd', function ($join) {
                $join->on('srd.product_id', '=', 'sd.product_id')
                    ->join('sale_summary as sr', 'srd.sale_summary_id', '=', 'sr.id')
                    ->where('sr.document_type', '=', 'SR') // Sale Return
                    ->whereColumn('sr.ref_document_no', 'ss.id');
            })
            ->where('sd.sale_summary_id', $sale_id)
            ->whereIn('ss.document_type', ['S','PS']) // sale or pos sale
            ->select(
                'sd.product_id',
                'products.barcode',
                'products.name as product_name',
                'sd.selling_unit_price',
                'sd.quantity as qty_sold',
                DB::raw('COALESCE(SUM(srd.quantity), 0) as qty_returned'),
                'sd.subtotal',
                DB::raw('(sd.quantity - COALESCE(SUM(srd.quantity), 0)) as stock_qty')
            )
            ->groupBy(
                'sd.product_id',
                'products.barcode',
                'products.name',
                'sd.selling_unit_price',
                'sd.quantity',
                'sd.subtotal'
            )
            ->get();
        // Add row counter and formatting
        $itemsWithCounter = $items->map(function ($item, $index) {
            return [
                'row_no'       => $index + 1,
                'product_id'   => $item->product_id,
                'barcode'      => $item->barcode,
                'product_name' => $item->product_name,
                'net_unit_cost'=> number_format($item->selling_unit_price, 2),
                'qty_sold'     => $item->qty_sold,
                'stock_qty'    => $item->stock_qty,
                'subtotal'     => number_format($item->subtotal, 2),
            ];
        });
        return response()->json($itemsWithCounter);
    }



    public function saleReturnStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'        => 'required|date',
            'reference'   => 'required|string',
            'sale_id'     => 'required|integer',
            'status'      => 'required|string',
            'order_tax'   => 'nullable|numeric',
            'discount'    => 'nullable|numeric',
            'shipping'    => 'nullable|numeric',
            'qty_return'  => 'required|array'
        ]);

        if (!$validator->passes()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        DB::beginTransaction();

        try {
            $grandTotal = 0;
            $getSale = DB::table('sale_summary')->find($request->sale_id);

            if (!$getSale) {
                return response()->json(['error' => 'Sale not found.'], 404);
            }

            // 1. New sale record for Return
            $returnSaleId = DB::table('sale_summary')->insertGetId([
                'created_by'      => auth()->id(),
                'store_id'        => $request->store_id ?? null,
                'customer_id'     => $getSale->customer_id,
                'document_type'   => 'SR', // Sale Return
                'invoice_number'  => $request->reference,
                'sale_date'       => $request->date,
                'total_amount'    => 0, // calculate later
                'discount'        => $request->discount ?? 0,
                'tax'             => $request->order_tax ?? 0,
                'shipping_charge' => $request->shipping ?? 0,
                'grand_total'     => 0, // calculate later
                'notes'           => $request->note ?? '',
                'ref_document_no' => $request->sale_id, // original sale id
                'status'          => $request->status,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 2. Save returned items
            foreach ($request->qty_return as $index => $qty) {
                $productId = $request->product_id[$index]; // ✅ same index ka product_id

                if ($qty > 0) {
                    $saleItem = DB::table('sale_details')
                        ->where('sale_summary_id', $request->sale_id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($saleItem) {
                        $subtotal = $saleItem->selling_unit_price * $qty;
                        $grandTotal += $subtotal;

                        DB::table('sale_details')->insert([
                            'sale_summary_id' => $returnSaleId,
                            'product_id'      => $saleItem->product_id,
                            'warehouse_id'    => $saleItem->warehouse_id,
                            'quantity'        => $qty,
                            'cost_unit_price'      => $saleItem->cost_unit_price,
                            'selling_unit_price'      => $saleItem->selling_unit_price,
                            'sale_date'  => $request->date,
                            'subtotal'        => $subtotal,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
            }

            $subTotal  = $grandTotal; // total of returned items
            $taxAmount = ($request->order_tax ?? 0) > 0 
                ? ($subTotal * ($request->order_tax / 100)) 
                : 0;

            $grandTotal = $subTotal + $taxAmount + ($request->shipping ?? 0) - ($request->discount ?? 0);

            DB::table('sale_summary')->where('id', $returnSaleId)->update([
                'total_amount' => $subTotal,
                //'tax'        => $taxAmount,
                'grand_total'  => $grandTotal,
                'updated_at'   => now(),
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Sale return saved successfully.',
                'sale_id'  => $request->sale_id,
                'return_id'=> $returnSaleId
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    private function getSaleReturnData($return_id)
    {
        // Main Sale Return record
        $saleReturn = DB::table('sale_summary')
            ->join('customers', 'sale_summary.customer_id', '=', 'customers.id')
            ->leftJoin('sale_summary as original_sale', 'sale_summary.ref_document_no', '=', 'original_sale.id')
            ->select(
                'sale_summary.*',
                'customers.name as customer_name',
                'original_sale.invoice_number as org_sale_invoice'
            )
            ->where('sale_summary.id', $return_id)
            ->where('sale_summary.document_type', 'SR') // Sale Return
            ->first();

        if (!$saleReturn) {
            return [
                'success' => false,
                'message' => 'Sale Return not found'
            ];
        }

        // Return items
        $returnItems = DB::table('sale_details')
            ->join('products as product', 'sale_details.product_id', '=', 'product.id')
            ->join('units as product_unit', 'product.unit_id', '=', 'product_unit.id')
            ->select(
                'sale_details.*',
                'product.name as product_name',
                'product.barcode as product_code',
                'product_unit.name as unit_name'
            )
            ->where('sale_details.sale_summary_id', $return_id)
            ->get();

        return [
            'success' => true,
            'return' => $saleReturn,
            'items'  => $returnItems
        ];
    }

    public function viewDetail($id)
    {
        $result = $this->getSaleReturnData($id);
        return response()->json($result);
    }



}
