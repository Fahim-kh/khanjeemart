@extends('admin.layouts.master')

@section('page-title')
    Purchase Management
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
                    <li class="breadcrumb-item active" aria-current="page">Product Purchase</li>
                </ol>
            </nav>
            <h4 class="fw-semibold mb-0">Create New Purchase Order</h4>
        </div>

        <!-- Alerts -->
        @include('admin.layouts.errorLayout')
        @include('admin.layouts.successLayout')
        @include('admin.layouts.validationLayout')   
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Purchase Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="purchaseForm">
                            @csrf
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control flatpickr-date" id="date" name="date" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="supplier" class="form-label required">Supplier</label>
                                    <select class="form-select supplier" id="supplier" name="supplier_id">

                                    </select>
                                </div>
                                @php
                                    $randomNumber = rand(10000, 19999);
                                @endphp
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="P-{{ $randomNumber }}" readonly>
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
                                    <div class="row align-items-end d-none result-info" style="border: 1px solid #eee;padding: 10px; border-radius: 20px;">
                                        <div class="col-lg-12">
                                            <span class="product_name badge bg-success" style="font-weight: 600" id="product_name" name="product_name"></span>
                                            <span class="text-success stock badge bg-warning text-white" id="stock" style="font-weight: 600"></span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="quantity" class="form-label">Quantity *</label>
                                            <input type="number" class="form-control quantity" id="quantity" name="quantity" placeholder="Enter quantity" required>
                                            <input type="hidden" class="form-control product_id" id="product_id" name="product_id" >
                                            <input type="hidden" class="form-control id" id="id" name="id" >
                                            <input type="hidden" class="form-control purchase_id" id="purchase_id" name="purchase_id" value="999">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="cost_price" class="form-label">Cost Price *</label>
                                            <input type="number" class="form-control unit_cost" id="unit_cost" name="unit_cost" placeholder="Enter cost price" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="sell_price" class="form-label">Sell Price *</label>
                                            <input type="number" class="form-control sell_price" id="sell_price" name="sell_price" placeholder="Enter sell price" required>
                                        </div>
                                        <!-- Add Product Button in same row -->
                                        <div class="col-md-3 mb-3 text-end">
                                            <button type="button" class="btn btn-primary w-100" id="btnPurchase">
                                                <i class="bi bi-plus-circle me-2"></i> Add Purchase
                                            </button>
                                            <button type="button" class="btn btn-primary w-100" id="btnPurchaseUpdate">
                                                <i class="bi bi-plus-circle me-2"></i> Edit Purchase
                                            </button>
                                            
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
                                            <th>Qty</th>
                                            <th>Unit Cost</th>
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
                                                <input type="number" class="form-control" id="order_tax" name="order_tax" value="0" min="0" max="100" placeholder="Enter tax %">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="discount" class="form-label">Discount</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">{{ config('settings.currency_symbol') }}</span>
                                                <input type="number" class="form-control" id="discount" name="discount" value="0" min="0" placeholder="Enter discount amount">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="shipping" class="form-label">Shipping</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-truck"></i></span>
                                                <input type="number" class="form-control" id="shipping" name="shipping" value="0" min="0" placeholder="Enter shipping cost">
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
                                                <option value="received">Received</option>
                                                <option value="pending">Pending</option>
                                                <option value="ordered">Ordered</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="note" class="form-label">Notes</label>
                                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Additional notes about this purchase order..."></textarea>
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
                                            <p class="mb-1">Order Tax: <strong>{{ config('settings.currency_symbol') }}<span id="order_tax_total">0.00</span></strong>%</p>
                                            <p class="mb-1">Discount: <strong>{{ config('settings.currency_symbol') }}<span id="discount_total">0.00</span></strong></p>
                                            <p class="mb-1">Shipping: <strong>{{ config('settings.currency_symbol') }}<span id="shipping_total">0.00</span></strong></p>
                                            <h6 class="mb-0">Grand Total: <strong>{{ config('settings.currency_symbol') }}<span id="grand_total">0.00</span></strong></h6>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-outline-secondary" id="btnReset">
                                                <i class="bi bi-x-circle me-1"></i> Reset
                                            </button>
                                           
                                            <button type="button" class="btn btn-primary w-100" id="btnFinalSave">
                                                <i class="bi bi-check-circle me-1"></i> Submit Purchase
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
const getPurchaseViewUrl = "{{ route('getPurchaseView') }}";
const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
const product_search = "{{ route('product_search') }}";
const getSaleIndexUrl = "{{ route('sale.index') }}";

</script>
<script src="{{ asset('admin/myjs/purchase/purchase.js') }}"></script>

<script>
$(document).ready(function() {
    loadSuppliers();
    function loadSuppliers(selectedId = null) {
        $.ajax({
            type: "GET",
            url: "{{ route('loadSuppliers') }}",
            success: function (response) {
                let $select = $('#supplier');
                $select.empty().append('<option disabled selected>Choose Supplier</option>');
                response.forEach(function (item) {
                    let selected = selectedId == item.id ? 'selected' : '';
                    $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
                });
                $select.attr('data-url', '{{ route('supplier.store') }}').attr('data-callback', 'loadSuppliers');
                initSelect2('supplier', 'Select supplier', '{{ route('supplier.store') }}', 'loadSuppliers');
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
    $(document).on('click', '.add-inline-btn', function () {
        let attributeID = $(this).data('id');
        let url = $(this).data('url');
        let loadCallbackName = $(this).data('callback');
        let newValue = $('.select2-container--open .select2-search__field').val();

        if (!newValue) return;

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: newValue,
                status: 'on'
            },
            success: function (response) {
                console.log(response);
                let $select = $('#' + attributeID);
                $select.append(new Option(response.data.name, response.data.id, true, true));
                $select.trigger('change');
                $select.select2('close');
                if (typeof window[loadCallbackName] === 'function') {
                    window[loadCallbackName](response.id);
                }
            toastr.success(`${attributeID.charAt(0).toUpperCase() + attributeID.slice(1)} added successfully`);

            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    toastr.error(xhr.responseJSON.error.join('<br>'));
                } else {
                    toastr.error(`Failed to create ${attributeID}`);
                }
            }
        });
    });
    $(document).on('input', '.select2-search__field', function () {
        let val = $(this).val();
        $('.add-inline-btn .new-entry-text').text(val);
    });
});

</script>


@endsection

