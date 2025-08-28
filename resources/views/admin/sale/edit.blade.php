@extends('admin.layouts.master')

@section('page-title')
    Sale Management
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
</style>
<div class="dashboard-main-body">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Product Sale</li>
                </ol>
            </nav>
            <h4 class="fw-semibold mb-0">Edit Sale Order</h4>
        </div>

        <!-- Alerts -->
        @include('admin.layouts.errorLayout')
        @include('admin.layouts.successLayout')
        @include('admin.layouts.validationLayout')   
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Sale Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="saleForm">
                            @csrf
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control flatpickr-date" id="date" name="sale_date" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="customer" class="form-label required">Customer</label>
                                    <select class="form-select customer_id" id="customer_id" name="customer_id">

                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="S-{{ now()->format('YmdHis') }}" readonly>
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
                                            <input type="hidden" class="form-control sale_id" id="sale_id" name="sale_id" value="{{ $sale->id }}">
                                         </div>
                                    </div>
                                    {{-- <div class="row align-items-end">
                                        <div class="col-md-3 mb-3">
                                            <label for="quantity" class="form-label">Quantity *</label>
                                            <input type="number" class="form-control quantity" id="quantity" name="quantity" placeholder="Enter quantity" required>
                                            <input type="hidden" class="form-control product_id" id="product_id" name="product_id" >
                                            <input type="hidden" class="form-control id" id="id" name="id" >
                                            <input type="hidden" class="form-control unit_cost" id="unit_cost" name="unit_cost" placeholder="Enter cost price" required>
                                        </div>
                    
                                        <div class="col-md-3 mb-3">
                                            <label for="sell_price" class="form-label">Sell Price *</label>
                                            <input type="number" class="form-control sell_price" id="sell_price" name="sell_price" placeholder="Enter sell price" required>
                                        </div>
                                        <!-- Add Product Button in same row -->
                                        <div class="col-md-3 mb-3 text-end">
                                            <button type="button" class="btn btn-primary w-100" id="btnSale">
                                                <i class="bi bi-plus-circle me-2"></i> Add Sale
                                            </button>
                                            <button type="button" class="btn btn-primary w-100" id="btnSaleUpdate">
                                                <i class="bi bi-plus-circle me-2"></i> Edit Sale
                                            </button>
                                            
                                        </div>
                                    </div> --}}
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
                                            <th>Sale Price</th>                                          
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="showdata"></tbody>
                                </table>
                            </div>
            
                            
                            <!-- Order Calculations Section -->
                            <div class="card border mb-4 shadow-sm">
                                <div class="card-header bg-light fw-semibold">Order Calculations</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="order_tax" class="form-label">Order Tax</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-percent"></i></span>
                                                <input type="number" class="form-control" id="order_tax" name="order_tax" value="{{ $sale->tax }}" min="0" max="100" placeholder="Enter tax %">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="discount" class="form-label">Discount</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">{{ config('settings.currency_symbol') }}</span>
                                                <input type="number" class="form-control" id="discount" name="discount" value="{{ $sale->discount }}" min="0" placeholder="Enter discount amount">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="shipping" class="form-label">Shipping</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-truck"></i></span>
                                                <input type="number" class="form-control" id="shipping" name="shipping" value="{{ $sale->shipping_charge }}" min="0" placeholder="Enter shipping cost">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            

                           <!-- Additional Information Section -->
                            <div class="card border shadow-sm">
                                <div class="card-header bg-light fw-semibold">Additional Information</div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="status" class="form-label required">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="received" {{ $sale->status == 'received' ? 'selected' : '' }}>Received</option>
                                                <option value="pending" {{ $sale->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="ordered" {{ $sale->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="note" class="form-label">Notes</label>
                                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Additional notes about this sale order...">{{ $sale->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Summary and Submission -->
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">Items: <strong><span id="total_items">0</span></strong></p>
                                            <p class="mb-1">Sub Total: <strong><span id="subTotal">0</span></strong></p>
                                            <p class="mb-1">Order Tax: <strong>{{ config('settings.currency_symbol') }}<span id="order_tax_total">0.00</span></strong></p>
                                            <p class="mb-1">Discount: <strong>{{ config('settings.currency_symbol') }}<span id="discount_total">0.00</span></strong></p>
                                            <p class="mb-1">Shipping: <strong>{{ config('settings.currency_symbol') }}<span id="shipping_total">0.00</span></strong></p>
                                            <h6 class="mb-0">Grand Total: <strong>{{ config('settings.currency_symbol') }}<span id="grand_total">0.00</span></strong></h6>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-outline-secondary" id="btnReset">
                                                <i class="bi bi-x-circle me-1"></i> Reset
                                            </button>
                                           
                                            <button type="button" class="btn btn-primary w-100" id="btnFinalEdit">
                                                <i class="bi bi-check-circle me-1"></i> Submit Sale
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
const getSaleViewUrl = "{{ route('getSaleView') }}";
const getSaleIndexUrl = "{{ route('sale.index') }}";
const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
const product_search = "{{ route('product_search') }}";
</script>
<script src="{{ asset('admin/myjs/sale/sale.js') }}"></script>

<script>
$(document).ready(function() {
    loadCustomers({{ $sale->customer_id}});
    let searchTimeout;

$('#product_search').on('input', function() {
    clearTimeout(searchTimeout);
    let searchTerm = $(this).val().trim();

    // If barcode is a standard length (EAN-8, UPC-A, EAN-13, etc.)
    const isBarcode = [8, 12, 13, 14].includes(searchTerm.length);

    if (isBarcode || searchTerm.length >= 2) {
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 100); // Small delay to allow fast scanner input
    } else {
        $('#searchResults').hide();
    }
});

function performSearch(searchTerm) {
    $.ajax({
        url: "{{ route('product_search') }}",
        method: "GET",
        data: { term: searchTerm },
        success: function(response) {
            let $results = $('#searchResults');
            $results.empty();
            
            if (response.length > 0) {
                response.forEach(function(product) {
                    $results.append(`
                        <a href="#" class="list-group-item list-group-item-action product-result" 
                           data-id="${product.id}" 
                           data-code="${product.barcode}"
                           data-product='${product.id}'>
                            <div class="d-flex w-100 justify-content-between">
                                <p class="mb-1"><img src="${imageUrl+'/'+product.product_image}" class="img-fluid" width="40px"> ${product.barcode}-${product.name}</p>
                                <small></small>
                            </div>
                        </a>
                    `);
                });
                $results.show();
            } else {
                $results.hide();
            }
        },
        error: function() {
            $('#searchResults').hide();
            toastr.error("Failed to search products");
        }
    });
}



    function loadCustomers(selectedId = null) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('loadCustomers') }}",
                    success: function (response) {
                        let $select = $('#customer_id');
                        $select.empty().append('<option disabled selected>Choose Customer</option>');
                        response.forEach(function (item) {
                            let selected = selectedId == item.id ? 'selected' : '';
                            $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
                        });
                        $select.attr('data-url', '{{ route('customer.store') }}').attr('data-callback', 'loadCustomers');
                        initSelect2('customer', 'Select Customer', '{{ route('customer.store') }}', 'loadCustomers');
                        if (selectedId) $select.val(selectedId).trigger('change');
                    }
                });
            }

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

