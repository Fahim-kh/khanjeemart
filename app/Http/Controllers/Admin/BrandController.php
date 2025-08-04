<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables as DataTables;
use Illuminate\Validation\Rule;
class BrandController extends Controller
{
  public function index()
    {
        return view('admin.brand.index');
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
                'name' => 'required|unique:brands',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $status = 0;
            if($request->post('status') == "on")
            {
                $status = 1;
            }

           $brand= Brand::create([
                'name' => $request->post('name'),                
                'status' => $status
            ]);
            return response()->json([
                'success' => 'Brand created successfully',
                'data' => [ 
                    'id' => $brand->id,
                    'name' => $brand->name
                ]
            ], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
                $brand = Brand::select(['id', 'name', 'status'])->orderBy('id','desc');
                return DataTables::of($brand)
                    ->addIndexColumn() // Adds DT_RowIndex internally
                    ->editColumn('status', function ($data) {
                        return current_status($data->status); // Assumes this returns HTML
                    })
                    ->addColumn('action', function ($data) {
                        return table_edit_delete_button($data->id, 'brand','Brand'); // Assumes this returns HTML
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
            $brand = Brand::find($id);
            return response()->json(['success' => 'successfull retrieve data', 'data' => $brand->toJson()], 200);
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
                    Rule::unique('brands')->ignore($request->id),
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
            $brand = Brand::findOrFail($request->id);                                               
            $brand->name = $request->name;           
            $brand->status = $status;
            $brand->update();
            return response()->json(['success' => 'data is successfully updated'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::find($id);
            $brand->delete();
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
                Brand::where('id', $value)->delete();
            }
            return response()->json(['success' => 'data is successfully deleted'], 200);
        }
        return 'false';
    }
    public function loadBrands(){
        $brands=  Brand::where('status',1)->get();
        return response()->json($brands);
    }
}

