<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseModel;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables as DataTables;
use Illuminate\Validation\Rule;
use Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.expense.index');
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
                'date' => 'required|date',
                'amount' => 'required',
                'expense_category' => 'required',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            // $status = 0;
            // if($request->post('status') == "on")
            // {
            //     $status = 1;
            // }

            $expense = ExpenseModel::create([
                'created_by' => Auth::user()->id,                
                'expense_category_id' => $request->post('expense_category'),   
                'date' => $request->post('date'),                
                'amount' => $request->post('amount'),                
                'description' => $request->post('description'),                
            ]);
            return response()->json([
                'success' => 'Expense created successfully',
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
    public function show($id)
    {
        try {
            $expenses = ExpenseModel::with('expenseCategory')
                ->select(['id', 'date', 'expense_category_id','amount','description'])
                ->orderBy('id', 'desc');
    
            return DataTables::of($expenses)
                ->addIndexColumn()
                ->addColumn('expense_category', function ($data) {
                    return $data->expenseCategory ? $data->expenseCategory->name : '-';
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'expense','Expenses'); 
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
            $expense = ExpenseModel::with('expenseCategory')->find($id);
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
                'date' => 'required|date',
                'amount' => 'required',
                'expense_category' => 'required',
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }
            $expense = ExpenseModel::findOrFail($request->id);                                               
            $expense->date = $request->date;           
            $expense->expense_category_id = $request->expense_category;           
            $expense->amount = $request->amount;           
            $expense->description = $request->description;
            $expense->update();
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
            $expense = ExpenseModel::find($id);
            $expense->delete();
            return response()->json(['success' => 'data is successfully deleted'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
