<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategoryModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables as DataTables;
use Illuminate\Validation\Rule;
use Auth;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.expense_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:expense_categories',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $status = 0;
            if($request->post('status') == "on")
            {
                $status = 1;
            }

            $expense = ExpenseCategoryModel::create([
                'name' => $request->post('name'),                
                'status' => $status
            ]);
            return response()->json([
                'success' => 'Expense category created successfully',
                'data' => [ 
                    'id' => $expense->id,
                    'name' => $expense->name
                ]
            ], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $expense_categories = ExpenseCategoryModel::select(['id','name','status'])->orderBy('id', 'desc');
            return DataTables::of($expense_categories)
                ->addIndexColumn() // Adds DT_RowIndex internally
                ->editColumn('status', function ($data) {
                    return current_status($data->status); // Assumes this returns HTML
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'expense_category','Expense Categories'); 
                })
                ->rawColumns(['action', 'status']) // ONLY those that return HTML
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
            $expense = ExpenseCategoryModel::find($id);
            return response()->json(['success' => 'successfull retrieve data', 'data' => $expense->toJson()], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function rec_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $expense_category = ExpenseCategoryModel::findOrFail($request->id);    
            $status = 0;
            if($request->status == "on")
            {
                $status = 1;
            }                                           
            $expense_category->name = $request->name;           
            $expense_category->status = $status;           
            $expense_category->update();
            return response()->json(['success' => 'data is successfully updated'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $expense = ExpenseCategoryModel::find($id);
            $expense->delete();
            return response()->json(['success' => 'data is successfully deleted'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function loadExpenseCategory(){
        $expense=  ExpenseCategoryModel::where('status',1)->get();
        return response()->json($expense);
    }
}
