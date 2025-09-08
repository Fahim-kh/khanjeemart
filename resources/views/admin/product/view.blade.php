@extends('admin.layouts.master')

@section('page-title')
    Product
@endsection
@section('main-content')
<style>
.font-weight-bold{
    font-weight: 600 !important;
}
</style>
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Product Summery</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('product.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Product Summery
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-hover" >
                            <tbody>
                                <tr>
                                    <th class="font-weight-bold">Type</th>
                                    <td>{{ $product->type }}</td>
                                    
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Product Barcode</th>
                                    <td><span class="badge bg-success">{{ $product->barcode }}</span></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Product Name</th>
                                    <td><span class="badge bg-secondary">{{ $product->name }}</span></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Category</th>
                                    <td>{{ $product->category->name }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Brand</th>
                                    <td>{{ ($product->brand_id != null)? $product->brand->name  : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Unit</th>
                                    <td>{{ ($product->unit_id != null)? $product->unit->name  : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Cost Price</th>
                                    <td><span class="badge bg-primary">{{ $avgUnitCost !== null ? number_format($avgUnitCost, 2) : 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Sale Price</th>
                                    <td><span class="badge bg-info">{{ $lastSalePrice !== null ? number_format($lastSalePrice, 2) : 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-bold">Stock Quantity</th>
                                    <td><span class="badge bg-warning">{{ ($stock != null)? $stock : 'N/A' }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="col-lg-4">
                        <img src="{{ asset('admin/uploads/products') }}/{{ ($product->product_image != null )? $product->product_image : 'default.png' }}" alt="{{ $product->name }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
