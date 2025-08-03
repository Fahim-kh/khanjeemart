@extends('admin.layouts.master')

@section('page-title')
    Purchase Management
@endsection

@section('main-content')
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

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Purchase Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="purchaseForm" method="POST" action="#">
                            @csrf
                            
                            <!-- Basic Information Section -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control flatpickr-date" id="date" name="date" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="supplier" class="form-label required">Supplier</label>
                                    <select class="form-select supplier" id="supplier" name="supplier_id" required>
                                        <option value="0">Select Supplier</option>
                                        <option value="1">Select Supplier</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="PR-{{ now()->format('YmdHis') }}" readonly>
                                </div>
                            </div>
            
                            <!-- Product Search Section -->
                            <div class="card mb-4 border">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="code" class="form-label">Code Product *</label>
                                            <div class="input-group">
                                                <button type="button" class="input-group-text" id="scanBarcodeBtn"
                                                id="scanBarcodeBtn" data-bs-toggle="modal" data-bs-target="#barcodeScanModal">
                                                    <img src="{{ asset('admin/assets/images/scan.png') }}" alt="Barcode"
                                                        style="height: 20px;">
                                                </button>
                                                <input type="text" class="form-control product_search" id="product_search" name="code"
                                                    placeholder="Scan or enter code" required autofocus >

                                            </div>
                                            <small class="form-text text-muted">Scan your barcode and select the correct
                                                symbology below</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Order Items Table -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle" id="order_items_table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="3%">#</th>
                                            <th width="25%">Product</th>
                                            <th width="12%">Unit Cost</th>
                                            <th width="10%">Stock</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Discount</th>
                                            <th width="10%">Tax</th>
                                            <th width="15%">Subtotal</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Items will be added dynamically via JavaScript -->
                                        <tr id="empty_row">
                                            <td colspan="9" class="text-center text-muted py-4">
                                                <i class="bi bi-cart-x fs-1"></i>
                                                <p class="mb-0">No products added yet</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
            
                            <!-- Order Calculations -->
                            {{-- <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="order_tax" class="form-label">Order Tax</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="order_tax" name="order_tax" value="0" min="0" max="100">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="discount" class="form-label">Discount</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount" name="discount" value="0" min="0">
                                        <span class="input-group-text">{{ config('settings.currency_symbol') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="shipping" class="form-label">Shipping</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="shipping" name="shipping" value="0" min="0">
                                        <span class="input-group-text">{{ config('settings.currency_symbol') }}</span>
                                    </div>
                                </div>
                            </div> --}}
            
                            <!-- Additional Information -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="received">Received</option>
                                        <option value="pending">Pending</option>
                                        <option value="ordered">Ordered</option>
                                    </select>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="note" class="form-label">Notes</label>
                                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Additional notes about this purchase order..."></textarea>
                                </div>
                            </div>
            
                            <!-- Summary and Submission -->
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">Items: <strong><span id="total_items">0</span></strong></p>
                                            <p class="mb-1">Order Tax: <strong>{{ config('settings.currency_symbol') }}<span id="order_tax_total">0.00</span></strong></p>
                                            <p class="mb-1">Discount: <strong>{{ config('settings.currency_symbol') }}<span id="discount_total">0.00</span></strong></p>
                                            <p class="mb-1">Shipping: <strong>{{ config('settings.currency_symbol') }}<span id="shipping_total">0.00</span></strong></p>
                                            <h4 class="mb-0">Grand Total: <strong>{{ config('settings.currency_symbol') }}<span id="grand_total">0.00</span></strong></h4>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="reset" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-1"></i> Reset
                                            </button>
                                            <a href="javascript:void(0)" class="btn btn-primary">
                                                <i class="bi bi-check-circle me-1"></i> Submit Purchase
                                            </a>
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
$(document).ready(function() {
    // Initialize Select2 for supplier dropdown
    $('.supplier').select2({
        placeholder: "Select Supplier",
    });

    // Product search functionality
    $('.product_search').on('keyup', function(e) {
        console.log($(this).val());
        if (e.key === 'Enter') {
            const searchTerm = $(this).val().trim();
            if (searchTerm.length > 0) {
                searchProduct(searchTerm);
                $(this).val('');
            }
        }
    });

    function searchProduct(term) {
        // AJAX call to search product
        $.ajax({
            url: '/admin/products/search',
            method: 'GET',
            data: { term: term },
            success: function(response) {
                if (response.success && response.product) {
                    addProductToTable(response.product);
                } else {
                    toastr.error('Product not found!');
                }
            },
            error: function() {
                toastr.error('Error searching for product');
            }
        });
    }

    function addProductToTable(product) {
        // Hide empty row if it exists
        $('#empty_row').hide();
        
        // Check if product already exists in table
        if ($(`#product_row_${product.id}`).length) {
            const row = $(`#product_row_${product.id}`);
            const qtyInput = row.find('.quantity');
            qtyInput.val(parseInt(qtyInput.val()) + 1);
            qtyInput.trigger('change');
            return;
        }

        // Add new row
        const row = `
            <tr id="product_row_${product.id}">
                <td>1</td>
                <td>
                    ${product.name} (${product.code})
                    <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                    <input type="hidden" name="products[${product.id}][name]" value="${product.name}">
                    <input type="hidden" name="products[${product.id}][code]" value="${product.code}">
                </td>
                <td>
                    <input type="number" class="form-control unit-price" name="products[${product.id}][unit_price]" value="${product.cost_price}" min="0" step="0.01">
                </td>
                <td class="text-center">${product.current_stock || 0}</td>
                <td>
                    <input type="number" class="form-control quantity" name="products[${product.id}][quantity]" value="1" min="1">
                </td>
                <td>
                    <input type="number" class="form-control discount" name="products[${product.id}][discount]" value="0" min="0" step="0.01">
                </td>
                <td>
                    <input type="number" class="form-control tax" name="products[${product.id}][tax]" value="${product.tax_rate || 0}" min="0" step="0.01">
                </td>
                <td class="subtotal">${product.cost_price}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-product">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#order_items_table tbody').append(row);
        updateRowNumbers();
        calculateTotals();
    }

    // Event delegation for dynamic elements
    $('#order_items_table').on('change', '.quantity, .unit-price, .discount, .tax', function() {
        calculateRowTotal($(this).closest('tr'));
        calculateTotals();
    });

    $('#order_items_table').on('click', '.remove-product', function() {
        $(this).closest('tr').remove();
        updateRowNumbers();
        calculateTotals();
        
        // Show empty row if no products left
        if ($('#order_items_table tbody tr').length === 1) {
            $('#empty_row').show();
        }
    });

    function calculateRowTotal(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        const discount = parseFloat(row.find('.discount').val()) || 0;
        const taxRate = parseFloat(row.find('.tax').val()) || 0;
        
        const subtotal = (quantity * unitPrice) - discount;
        const taxAmount = subtotal * (taxRate / 100);
        const rowTotal = subtotal + taxAmount;
        
        row.find('.subtotal').text(rowTotal.toFixed(2));
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalItems = 0;
        
        $('#order_items_table tbody tr').not('#empty_row').each(function() {
            const rowTotal = parseFloat($(this).find('.subtotal').text()) || 0;
            subtotal += rowTotal;
            totalItems += parseInt($(this).find('.quantity').val()) || 0;
        });
        
        const orderTax = parseFloat($('#order_tax').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const shipping = parseFloat($('#shipping').val()) || 0;
        
        const taxAmount = subtotal * (orderTax / 100);
        const grandTotal = subtotal + taxAmount - discount + shipping;
        
        // Update summary
        $('#total_items').text(totalItems);
        $('#order_tax_total').text(taxAmount.toFixed(2));
        $('#discount_total').text(discount.toFixed(2));
        $('#shipping_total').text(shipping.toFixed(2));
        $('#grand_total').text(grandTotal.toFixed(2));
    }

    function updateRowNumbers() {
        $('#order_items_table tbody tr').not('#empty_row').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Calculate totals when order tax, discount, or shipping changes
    $('#order_tax, #discount, #shipping').on('change', calculateTotals);
});
</script>
@endsection