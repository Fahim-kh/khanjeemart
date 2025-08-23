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
        //    $purchases = DB::table('purchases')
        //     ->select(
        //         'purchases.id',
        //         'purchases.purchase_date',
        //         'purchases.invoice_number',
        //         'purchases.grand_total',
        //         'purchases.status',
        //         'suppliers.name as supplier_name'
        //     )
        //     ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
        //     ->where('purchases.document_type', 'P')
        //     ->orderBy('purchases.id', 'desc')
        //     ->get(); 
        //     //dd($purchases);  
        //     return DataTables::of($purchases)
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($data) {
        //             return table_action_dropdown($data->id, 'purchase', 'Purchase');
        //         })
        //         ->rawColumns(['action'])
        //         ->make(true);
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
        // try {
        // $deleted = DB::table('purchase_items_temp')->where('id', $id)->delete();

        // if ($deleted) {
        //     return response()->json(['success' => 'Supplier deleted successfully'], 200);
        // } else {
        //     return response()->json(['error' => 'Record not found.'], 404);
        // }

        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
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
                'quantity' => 'required|numeric|min:1',
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
}
