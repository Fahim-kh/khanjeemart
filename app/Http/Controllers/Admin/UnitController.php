<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables as DataTables;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index()
    {
        return view('admin.unit.index');
    }

    public function create()
    {
        try {
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:units',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $status = 0;
            if($request->post('status') == "on")
            {
                $status = 1;
            }

            $unit = Unit::create([
                'name' => $request->post('name'),                
                'status' => $status
            ]);
            return response()->json([
                'success' => 'Unit created successfully',
                'data' => [ 
                    'id' => $unit->id,
                    'name' => $unit->name
                ]
            ], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $unit = Unit::select(['id', 'name', 'status'])->orderBy('id', 'desc');
            return DataTables::of($unit)
                ->addIndexColumn() // Adds DT_RowIndex internally
                ->editColumn('status', function ($data) {
                    return current_status($data->status); // Assumes this returns HTML
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'unit','Unit'); 
                })
                ->rawColumns(['action', 'status']) // ONLY those that return HTML
                ->make(true);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $unit = Unit::find($id);
            return response()->json(['success' => 'successfull retrieve data', 'data' => $unit->toJson()], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => [
                    'required',
                    Rule::unique('units')->ignore($request->id),
                ],
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $status = 0;
            if($request->status == "on")
            {
                $status = 1;
            }
            $unit = Unit::findOrFail($request->id);                                               
            $unit->name = $request->name;           
            $unit->status = $status;
            $unit->update();
            return response()->json(['success' => 'data is successfully updated'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            $unit->delete();
            return response()->json(['success' => 'data is successfully deleted'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function deleteAll(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'check_all' => 'required'
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            foreach ($request->check_all as $value) {
                Unit::where('id', $value)->delete();
            }
            return response()->json(['success' => 'data is successfully deleted'], 200);
        }
        return 'false';
    }
    public function loadUnits(){
        $units=  Unit::where('status',1)->get();
        return response()->json($units);
    }
}
