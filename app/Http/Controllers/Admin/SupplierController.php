<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin.supplier.index');
    }

    public function create()
    {
        try {
            // Optional: load form or other data
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'       => 'required|unique:suppliers',
                'email'      => 'nullable|email',
                'phone'      => 'nullable',
                'address'    => 'nullable',
                'country'    => 'nullable',
                'city'       => 'nullable',
                'tax_number' => 'nullable',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $status = $request->has('status') && $request->status === 'on' ? 1 : 0;

            $supplier= Supplier::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'country'    => $request->country,
                'city'       => $request->city,
                'tax_number' => $request->tax_number,
                'opening_balance' => $request->opening_balance,
                'status'     => $status,
            ]);
            return response()->json([
                'success' => 'Supplier created successfully',
                'data' => [ 
                    'id' => $supplier->id,
                    'name' => $supplier->name
                ]
            ], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $supplier = Supplier::select(['id', 'name', 'email', 'phone', 'address', 'country', 'city','opening_balance', 'tax_number', 'status'])->orderBy('id', 'desc');

            return DataTables::of($supplier)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    return current_status($data->status);
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'supplier', 'Supplier');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $supplier = Supplier::find($id);
            return response()->json(['success' => 'Successfully retrieved data', 'data' => $supplier->toJson()], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'         => 'required',
                'name'       => [
                    'required',
                    Rule::unique('suppliers')->ignore($request->id),
                ],
                'email'      => 'nullable|email',
                'phone'      => 'nullable',
                'address'    => 'nullable',
                'country'    => 'nullable',
                'city'       => 'nullable',
                'tax_number' => 'nullable',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $status = $request->status === 'on' ? 1 : 0;

            $supplier = Supplier::findOrFail($request->id);
            $supplier->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'country'    => $request->country,
                'city'       => $request->city,
                'tax_number' => $request->tax_number,
                'opening_balance' => $request->opening_balance,
                'status'     => $status,
            ]);

            return response()->json(['success' => 'Supplier updated successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);
            $supplier->delete();
            return response()->json(['success' => 'Supplier deleted successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function deleteAll(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'check_all' => 'required|array'
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            Supplier::whereIn('id', $request->check_all)->delete();

            return response()->json(['success' => 'Suppliers deleted successfully'], 200);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function loadSuppliers()
    {
        $suppliers = Supplier::where('status', 1)->get();
        return response()->json($suppliers);
    }
}
