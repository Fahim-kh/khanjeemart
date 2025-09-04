<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategoryModel;
use Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        //
    }
    public function loadExpenseCategory(){
        $expense=  ExpenseCategoryModel::where('status',1)->get();
        return response()->json($expense);
    }
}
