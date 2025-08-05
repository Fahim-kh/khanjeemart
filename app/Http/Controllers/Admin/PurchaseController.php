<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseModel;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Validator;

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
    public function show(string $id)
    {
        //
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
                'supplier_id' => 'required|integer|exists:suppliers,id', 
                'product_id' => 'required|integer|exists:products,id', 
                'date' => 'required',
                'quantity' => 'required|numeric',
                'unit_cost' => 'required|numeric'
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $sub_total = $request->quantity * $request->unit_cost;
            // Insert into purchase_items_temp
            DB::table('purchase_items_temp')->insert([
                'purchase_id' => 999, // or dynamically assign if needed
                'supplier_id' => $request->supplier_id,
                'purchase_bill_date' => $request->date,
                'product_id' => $request->product_id,
                'variant_id' => null,
                'warehouse_id' => null,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
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

    public function getPurchaseView()
    {
        try {
            $data = DB::table('purchase_items_temp')->get();

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



}
