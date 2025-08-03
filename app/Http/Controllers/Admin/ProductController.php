<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductModel;
use DataTables;
use Auth;
use Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('admin.product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bar_code' => 'required',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_image' => 'nullable|image|max:5048', // 5MB
        ]);
        $productNumber = $this->generateUniqueProductNumber();
        $fileName = null;
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $fileName = md5($file->getClientOriginalName()) . "_" . date('d-m-y') . "_" . time() . "." . $file->getClientOriginalExtension();
            $file->move(public_path() . "/admin/uploads/products", $fileName);
        }
        $status = $request->has('status') && $request->status === 'on' ? 1 : 0;
        ProductModel::create([
            'created_by' => Auth::user()->id,
            'store_id' => 1,
            'brand_id' => $request->brand_id,	
            'category_id' => $request->category_id,
            'unit_id'	 => $request->unit_id,
            'product_number' => $productNumber,
            'name' => $request->name,
            'slug' => Str::slug($request->name,'-'),
            'product_image' => $fileName,
            'sku'	=> 'SKU'.$productNumber,
            'barcode' => $request->bar_code,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $status,
        ]);

        return response()->json(['message' => 'Product created successfully']);

        
    }
    private function generateUniqueProductNumber(): string
    {
        do {
            $number = mt_rand(100000, 999999);
        } while (ProductModel::where('product_number', $number)->exists());

        return (string) $number;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $products = ProductModel::select(['id', 'name', 'barcode','status'])->orderBy('id', 'desc');
            return DataTables::of($products)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    return current_status($data->status);
                })
                ->addColumn('action', function ($data) {
                    return table_edit_delete_button($data->id, 'products', 'Products');
                })
                ->rawColumns(['action', 'status'])
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
        $product = ProductModel::find($id);
        return view('admin.product.edit',compact($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'bar_code' => 'required',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_image' => 'nullable|image|max:5048', // 5MB
        ]);
        $productNumber = $this->generateUniqueProductNumber();
       
        $status = $request->has('status') && $request->status === 'on' ? 1 : 0;
        $product = ProductModel::find($id);
        $currentImage = $product->product_image;
        $fileName = null;
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $fileName = md5($file->getClientOriginalName()) . "_" . date('d-m-y') . "_" . time() . "." . $file->getClientOriginalExtension();
            $file->move(public_path() . "/admin/uploads/products", $fileName);
        }

        $product->update([
            'created_by' => Auth::user()->id,
            'store_id' => 1,
            'brand_id' => $request->brand_id,	
            'category_id' => $request->category_id,
            'unit_id'	 => $request->unit_id,
            'product_number' => $productNumber,
            'name' => $request->name,
            'slug' => Str::slug($request->name,'-'),
            'product_image' => ($fileName)? $fileName : $currentImage,
            'sku'	=> 'SKU'.$productNumber,
            'barcode' => $request->bar_code,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $status,
        ]);
        if($fileName){
            File::delete(public_path('/admin/uploads/products/' . $currentImage ));
        }

        return response()->json(['message' => 'Product updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = ProductModel::find($id);
        $currentImage = $product->product_image;
        File::delete(public_path('/admin/uploads/products/' . $currentImage ));
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
