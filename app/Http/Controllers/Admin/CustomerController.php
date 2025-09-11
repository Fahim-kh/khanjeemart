<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customer.index');
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
                'name'       => 'required|unique:customers',
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
            if($request->owner === null){
                $owner = 0;
            } else{
                $owner = $request->owner;
            }
            if($request->opening_balance === null){
                $opening_balance = 0;
            } else{
                $opening_balance = $request->opening_balance;
            }
            $customer =Customer::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'country'    => $request->country,
                'city'       => $request->city,
                'tax_number' => $request->tax_number,
                'owner'     => $owner,
                'opening_balance'     => $opening_balance,
                'status'     => $status,
            ]);

            return response()->json([
                'success' => 'Customer created successfully',
                'data' => [ 
                    'id' => $customer->id,
                    'name' => $customer->name
                ]
            ], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $customers = Customer::select(['id', 'name', 'email', 'phone', 'address', 'country', 'city','opening_balance', 'tax_number', 'status'])->orderBy('id', 'desc');

            return DataTables::of($customers)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    return current_status($data->status);
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'customer', 'Customer');
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
            $customer = Customer::find($id);
            return response()->json(['success' => 'Data retrieved successfully', 'data' => $customer->toJson()], 200);
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
                    Rule::unique('customers')->ignore($request->id),
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

            $customer = Customer::findOrFail($request->id);
            $customer->update([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'country'    => $request->country,
                'city'       => $request->city,
                'tax_number' => $request->tax_number,
                'owner'     => $request->owner,
                'opening_balance'     => $request->opening_balance,
                'status'     => $status,
            ]);

            return response()->json(['success' => 'Customer updated successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);
            $customer->delete();
            return response()->json(['success' => 'Customer deleted successfully'], 200);
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

            Customer::whereIn('id', $request->check_all)->delete();

            return response()->json(['success' => 'Selected customers deleted successfully'], 200);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function loadCustomers()
    {
        $customers = Customer::where('status', 1)->get();
        return response()->json($customers);
    }
}
