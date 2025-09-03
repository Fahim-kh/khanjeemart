@extends('admin.layouts.master')

@section('page-title')
    Stock Adjustment Management
@endsection

@section('main-content')
<style>
#searchResults {
    z-index: relative;
    width: 100%;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.product-result:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.product-result.active {
    background-color: #e9ecef;
}
.product_image{
    width: 120px;
    height: 80px;
    object-fit: inherit;
}
</style>
<div class="dashboard-main-body">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product Stock Adjustment</li>
                </ol>
            </nav>
            <h4 class="fw-semibold mb-0">Edit Stock Adjustment</h4>
        </div>

        <!-- Alerts -->
        @include('admin.layouts.errorLayout')
        @include('admin.layouts.successLayout')
        @include('admin.layouts.validationLayout')   
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Stock Adjustment Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="stockAdjustmentForm">
                            @csrf
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control flatpickr-date" id="date" name="adjustment_date" required value="{{ \Carbon\Carbon::parse($adjustment->adjustment_date)->format('Y-m-d') }}">
                                </div>
                               
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ $adjustment->reference}}" readonly>
                                </div>
                            </div>
            
                            <!-- Product Search Section -->
                            <div class="card mb-4 border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="product_search" class="form-label">Search Product *</label>
                                            <div class="input-group">
                                                <button type="button" class="input-group-text" id="scanBarcodeBtn" data-bs-toggle="modal" data-bs-target="#barcodeScanModal">
                                                    <img src="{{ asset('admin/assets/images/scan.png') }}" alt="Barcode" style="height: 20px;">
                                                </button>
                                                <input type="text" class="form-control product_search" id="product_search" name="product_search" 
                                                    placeholder="Scan or search product" required autofocus>
                                            </div>
                                            <div id="searchResults" class="list-group mt-2" style="display: none; max-height: 300px; overflow-y: auto;">
                                                <!-- Search results will appear here -->
                                            </div>
                                            <small class="form-text text-muted">Scan barcode or type to search products</small>
                                        </div>
                                    </div>

                                    <!-- Quantity, Cost Price, Sell Price in one row -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="product_name" class="form-label"></label>
                                            <input type="text" class="form-control product_name" id="product_name" name="product_name" readonly>
                                            <input type="hidden" class="form-control adjustment_id" id="adjustment_id" name="adjustment_id" value="{{ $adjustment->id }}">
                                         </div>
                                    </div>
                                </div>
                            </div>
                       


                           <!-- Order Items Table -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle" id="order_items_table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Qty</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="showdata"></tbody>
                                </table>
                            </div>
            
                            
                            <!-- Additional Information Section -->
                            <div class="card border shadow-sm">

                                <div class="card-body">
                                    <div class="row g-3">
                                       
                                        <div class="col-md-12">
                                            <label for="note" class="form-label">Notes</label>
                                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Additional notes about this sale order...">{{$adjustment->notes}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Summary and Submission -->
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-outline-secondary" id="btnReset">
                                                <i class="bi bi-x-circle me-1"></i> Reset
                                            </button>

                                            <button type="button" class="btn btn-primary" id="btnFinalEdit">
                                                <i class="bi bi-check-circle me-1"></i> Submit StockAdjustment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.layouts.delete')   
    @include('admin.layouts.lastSalePurchaseDialog')   

</div>
<style>
    .select2-container,
        .select2-selection,
        .select2-dropdown {
            width: 330px !important;
        }
</style>
@endsection

@section('script')

<script>
const baseUrl = "{{ env('APP_URL') }}";
const getStockAdjustmentViewUrl = "{{ route('getStockAdjustmentView') }}";
const getStockAdjustmentIndexUrl = "{{ route('stock_adjustment.index') }}";
const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
const product_search = "{{ route('product_search_for_sale') }}";

</script>
<script src="{{ asset('admin/myjs/stock_adjustment/stock_adjustment.js') }}"></script>

<script>
$(document).ready(function() {
    let searchTimeout;
    function initSelect2(attributeID, placeholder, storeUrl, reloadCallback) {
        $('#' + attributeID).select2({
            width: '100%',
            placeholder: placeholder,
            language: {
                noResults: function () {
                    return `<div class="text-center">
                        <em>No results found</em><br/>
                        <button 
                            type="button" 
                            class="btn btn-sm btn-primary mt-2 add-inline-btn" 
                            data-id="${attributeID}"
                            data-url="${storeUrl}"
                            data-callback="${reloadCallback}">
                            + Add "<span class="new-entry-text"></span>"
                        </button>
                    </div>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }
});

</script>



@endsection

