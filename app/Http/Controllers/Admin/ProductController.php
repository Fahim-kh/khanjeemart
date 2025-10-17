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
use File;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('admin.product.index');
    }
    public function product_search(Request $request)
    {
        $term = $request->input('term');
    
        $products = ProductModel::where('name', 'like', "%$term%")
            ->orWhere('barcode', 'like', "%$term%")
            // ->limit(10)
            ->get();
        return response()->json($products);
        // ProductModel::where('name','%'.)
    }
    public function product_search_for_sale(Request $request)
    {
        $term = $request->input('term');
    
        $products = ProductModel::where(function($q) use ($term) {
            $q->where('name', 'like', "%$term%")
              ->orWhere('barcode', 'like', "%$term%");
        })
        ->whereHas('purchaseItems') // ✅ only products with purchase items
        // ->limit(10)
        ->get();

        return response()->json($products);
        // ProductModel::where('name','%'.)
    }

    public function latestPosProducts(){
        $products = ProductModel::select(
            'products.id',
            'products.name',
            'products.barcode',
            'products.product_image',
            'u.name as unitName',

            DB::raw("
                (
                    COALESCE(ps.purchased_qty, 0)
                    - COALESCE(ps.returned_qty, 0)
                    - COALESCE(ss.sold_qty, 0)
                    + COALESCE(ss.sale_return_qty, 0)
                    + COALESCE(sa.adjustment_addition, 0)
                    - COALESCE(sa.adjustment_subtraction, 0)
                ) AS stock
            "),

            DB::raw("COALESCE(latest_pi.sale_price, 0) as sale_price")
        )
        ->join('units as u', 'u.id', '=', 'products.unit_id')

        // ✅ latest purchase sale_price
        ->leftJoin(DB::raw("(
            SELECT pi.product_id, pi.sale_price
            FROM purchase_items pi
            JOIN purchases pu ON pu.id = pi.purchase_id
            WHERE pu.document_type = 'P'
            AND pi.id IN (
                SELECT MAX(pi2.id)
                FROM purchase_items pi2
                JOIN purchases pu2 ON pu2.id = pi2.purchase_id
                WHERE pu2.document_type = 'P'
                GROUP BY pi2.product_id
            )
        ) latest_pi"), 'latest_pi.product_id', '=', 'products.id')

        // purchases (quantities)
        ->leftJoin(DB::raw("( 
            SELECT 
                pi.product_id,
                SUM(CASE WHEN pu.document_type = 'P' THEN pi.quantity ELSE 0 END) AS purchased_qty,
                SUM(CASE WHEN pu.document_type = 'PR' THEN pi.quantity ELSE 0 END) AS returned_qty
            FROM purchase_items pi
            JOIN purchases pu ON pu.id = pi.purchase_id
            GROUP BY pi.product_id
        ) ps"), 'ps.product_id', '=', 'products.id')

        // sales
        ->leftJoin(DB::raw("(
            SELECT 
                sd.product_id,
                SUM(CASE WHEN ss.document_type IN ('S','PS') THEN sd.quantity ELSE 0 END) AS sold_qty,
                SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END) AS sale_return_qty
            FROM sale_details sd
            JOIN sale_summary ss ON ss.id = sd.sale_summary_id
            GROUP BY sd.product_id
        ) ss"), 'ss.product_id', '=', 'products.id')

        // stock adjustments
        ->leftJoin(DB::raw("(
            SELECT 
                sai.product_id,
                SUM(CASE WHEN sai.adjustment_type = 'addition' THEN sai.quantity ELSE 0 END) AS adjustment_addition,
                SUM(CASE WHEN sai.adjustment_type = 'subtraction' THEN sai.quantity ELSE 0 END) AS adjustment_subtraction
            FROM stock_adjustment_items sai
            JOIN stock_adjustments sa ON sa.id = sai.adjustment_id
            GROUP BY sai.product_id
        ) sa"), 'sa.product_id', '=', 'products.id')

        ->latest('products.id')
        ->paginate(6);

    $result = $products->toArray();
    $result['currency_symbol'] = env('CURRENCY_SYMBLE');

    return response()->json($result);

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
            'unit_id' => 'required|exists:units,id',
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
                    return table_edit_delete_button($data->id, 'product', 'Products');
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
        return view('admin.product.edit',compact('product'));
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
        try {
        $product = ProductModel::find($id);
        $currentImage = $product->product_image;
        File::delete(public_path('/admin/uploads/products/' . $currentImage ));
        $product->delete();
            return response()->json(['success' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    public function product_view($id){
        $product = ProductModel::with('category','brand','unit')->find($id);
        // $avgUnitCost = DB::table('purchase_items')
        //     ->where('product_id', $id)
        //     ->avg('unit_cost');
        $avgUnitCost = DB::table('purchase_items')
                    ->where('product_id', $id)
                    ->orderByDesc('id') 
                    ->value('unit_cost');
        $lastSalePrice = DB::table('purchase_items')
            ->where('product_id', $id)
            ->orderByDesc('id') // or 'created_at'
            ->value('sale_price');
        $stock = $this->getProductStock($id);
        return view('admin.product.view',compact('product','avgUnitCost','lastSalePrice','stock'));
    }
    function getProductStock($productId)
    {
        // Purchase (final)
        $purchase = DB::table('purchase_items as pi')
            ->join('purchases as p', 'pi.purchase_id', '=', 'p.id')
            ->where('pi.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN p.document_type = 'P' THEN pi.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN p.document_type = 'PR' THEN pi.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $purchaseQty = $purchase ? $purchase->total : 0;

        // Sale (final)
        $sale = DB::table('sale_details as sd')
            ->join('sale_summary as ss', 'sd.sale_summary_id', '=', 'ss.id')
            ->where('sd.product_id', $productId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN ss.document_type = 'S' THEN sd.quantity ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN ss.document_type = 'SR' THEN sd.quantity ELSE 0 END), 0) as total
            ")
            ->first();
        $saleQty = $sale ? $sale->total : 0;

        // Sale (temp)
        $saleTempQty = DB::table('sale_details_temp')
            ->where('product_id', $productId)
            ->sum('quantity');

        // Final Stock
        $stock = ($purchaseQty) - ($saleQty + $saleTempQty);

        return $stock;
    }
}
