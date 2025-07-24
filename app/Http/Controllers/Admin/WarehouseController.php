<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('admin.warehouse.index');
    }

    public function create()
    {
        try {
            // Optional: return a modal/form
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:warehouses',
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $status = $request->status === 'on' ? 1 : 0;

            Warehouse::create([
                'name' => $request->name,
                'status' => $status,
            ]);

            return response()->json(['success' => 'Warehouse created successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $warehouses = Warehouse::select(['id', 'name', 'status'])->orderBy('id', 'desc');

            return DataTables::of($warehouses)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    return current_status($data->status);
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'warehouse', 'Warehouse');
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
            $warehouse = Warehouse::find($id);
            return response()->json(['success' => 'Data retrieved successfully', 'data' => $warehouse->toJson()], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'   => 'required',
                'name' => [
                    'required',
                    Rule::unique('warehouses')->ignore($request->id),
                ],
            ]);

            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            $status = $request->status === 'on' ? 1 : 0;

            $warehouse = Warehouse::findOrFail($request->id);
            $warehouse->update([
                'name'   => $request->name,
                'status' => $status,
            ]);

            return response()->json(['success' => 'Warehouse updated successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::find($id);
            $warehouse->delete();
            return response()->json(['success' => 'Warehouse deleted successfully'], 200);
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

            Warehouse::whereIn('id', $request->check_all)->delete();

            return response()->json(['success' => 'Selected warehouses deleted successfully'], 200);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function loadWarehouses()
    {
        $warehouses = Warehouse::where('status', 1)->get();
        return response()->json($warehouses);
    }
}
