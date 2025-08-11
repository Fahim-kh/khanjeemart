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
            <h4 class="fw-semibold mb-0">Edit Purchase Order</h4>
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
                                    <input type="date" class="form-control flatpickr-date" id="date" name="date" required value="{{ $purchase->purchase_date }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="supplier" class="form-label required">Supplier</label>
                                    <select class="form-select supplier" id="supplier" name="supplier_id">

                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ $purchase->invoice_number }}" readonly>
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
                                         </div>
                                    </div>
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-3">
                                            <label for="quantity" class="form-label">Quantity *</label>
                                            <input type="number" class="form-control quantity" id="quantity" name="quantity" placeholder="Enter quantity" required>
                                            <input type="hidden" class="form-control product_id" id="product_id" name="product_id" >
                                            <input type="hidden" class="form-control id" id="id" name="id" >
                                            <input type="hidden" class="form-control purchase_id" id="purchase_id" name="purchase_id" value="{{ $purchase->id }}">
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
                                                <input type="number" class="form-control" id="order_tax" name="order_tax" value="{{ $purchase->tax }}" min="0" max="100" placeholder="Enter tax %">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="discount" class="form-label">Discount</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">{{ config('settings.currency_symbol') }}</span>
                                                <input type="number" class="form-control" id="discount" name="discount" value="{{ $purchase->discount }}" min="0" placeholder="Enter discount amount">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="shipping" class="form-label">Shipping</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-truck"></i></span>
                                                <input type="number" class="form-control" id="shipping" name="shipping" value="{{ $purchase->shipping_charge }}" min="0" placeholder="Enter shipping cost">
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
                                                <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                                                <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="ordered" {{ $purchase->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="note" class="form-label">Notes</label>
                                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Additional notes about this purchase order...">{{ $purchase->notes }}</textarea>
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
const getPurchaseViewUrl = "{{ route('getPurchaseView') }}";
const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
</script>
<script src="{{ asset('admin/myjs/purchase/purchase.js') }}"></script>

<script>
$(document).ready(function() {
    loadSuppliers({{ $purchase->supplier_id }});
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

    

    // // Hide results when clicking elsewhere
    // $(document).on('click', function(e) {
    //     if (!$(e.target).closest('#product_search, #searchResults').length) {
    //         $('#searchResults').hide();
    //     }
    // });

    // function addProductToTable(product) {
    //     // Hide empty row if it exists
    //     $('#empty_row').hide();
        
    //     // Check if product exists in table
    //     if ($(`#product_row_${product.id}`).length) {
    //         const row = $(`#product_row_${product.id}`);
    //         const qtyInput = row.find('.quantity');
    //         const currentStock = parseInt(product.current_stock) || 0;
    //         const currentQty = parseInt(qtyInput.val()) || 0;
            
    //         if (currentQty < currentStock) {
    //             qtyInput.val(currentQty + 1);
    //             qtyInput.trigger('change');
    //         } else {
    //             toastr.warning(`Cannot add more than available stock (${currentStock})`);
    //         }
    //         return;
    //     }

    //     // Add new row with editable cost and price
    //     const row = `
    //         <tr id="product_row_${product.id}">
    //             <td class="align-middle">1</td>
    //             <td class="align-middle">
    //                 ${product.name} <small class="text-muted">(${product.barcode})</small>
    //                 <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
    //                 <input type="hidden" name="products[${product.id}][name]" value="${product.name}">
    //                 <input type="hidden" name="products[${product.id}][code]" value="${product.barcode}">
    //             </td>
    //             <td>
    //                 <input type="number" class="form-control unit-cost" 
    //                        name="products[${product.id}][unit_cost]" 
    //                        value="0.00" 
    //                        min="0" step="0.01" required>
    //             </td>
    //             <td>
    //                 <input type="number" class="form-control sale-price" 
    //                        name="products[${product.id}][sale_price]" 
    //                        value="0.00" 
    //                        min="0" step="0.01" required>
    //             </td>
    //             <td class="text-center align-middle stock">
    //                 <input type="number" class="form-control quantity" 
    //                        name="products[${product.id}][quantity]" 
    //                        value="1" min="1" max="${product.current_stock || 1}" required>
    //                        </td>
    //             <td>
    //                 <input type="number" class="form-control tax" 
    //                        name="products[${product.id}][tax]" 
    //                        value="0.00" 
    //                        min="0" step="0.01">
    //             </td>
    //             <td>
    //                 <input type="number" class="form-control subtotal" 
    //                        name="products[${product.id}][subtotal]" 
    //                        value="0.00" 
    //                        min="0" step="0.01">
    //             </td>
    //             <td class="subtotal align-middle">0.00</td>
    //             <td class="text-center align-middle">
    //                 <button type="button" class="btn btn-sm btn-danger remove-product">
    //                     <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
    //                 </button>
    //             </td>
    //         </tr>
    //     `;
        
    //     $('#order_items_table tbody').append(row);
    //     updateRowNumbers();
    //     calculateTotals();
    //     toastr.success(`${product.name} added to purchase order`);
    // }

    // // Calculate row total when values change
    // $('#order_items_table').on('change', '.unit-cost, .sale-price, .quantity, .discount, .tax', function() {
    //     const row = $(this).closest('tr');
    //     calculateRowTotal(row);
    //     calculateTotals();
    // });

    // // Calculate row subtotal
    // function calculateRowTotal(row) {
    //     const unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
    //     const quantity = parseFloat(row.find('.quantity').val()) || 0;
    //     const discount = parseFloat(row.find('.discount').val()) || 0;
    //     const taxRate = parseFloat(row.find('.tax').val()) || 0;
        
    //     const subtotal = unitCost * quantity;
    //     const discountAmount = Math.min(discount, subtotal);
    //     const taxableAmount = subtotal - discountAmount;
    //     const taxAmount = taxableAmount * (taxRate / 100);
    //     const rowTotal = taxableAmount + taxAmount;
        
    //     row.find('.subtotal').text(rowTotal.toFixed(2));
    // }

    // // Remove product from order
    // $('#order_items_table').on('click', '.remove-product', function() {
    //     const productName = $(this).closest('tr').find('td:nth-child(2)').text().trim();
    //     $(this).closest('tr').remove();
    //     //updateRowNumbers();
    //     //calculateTotals();
    //     toastr.info(`${productName} removed from order`);
        
    //     // Show empty row if no products left
    //     if ($('#order_items_table tbody tr').not('#empty_row').length === 0) {
    //         $('#empty_row').show();
    //     }
    // });

    // // Calculate order totals
    // function calculateTotals() {
    //     let subtotal = 0;
    //     let totalItems = 0;
    //     let totalTax = 0;
        
    //     $('#order_items_table tbody tr').not('#empty_row').each(function() {
    //         const rowTotal = parseFloat($(this).find('.subtotal').text()) || 0;
    //         subtotal += rowTotal;
    //         totalItems += parseInt($(this).find('.quantity').val()) || 0;
            
    //         // Calculate individual tax for accurate tax total
    //         const rowUnitCost = parseFloat($(this).find('.unit-cost').val()) || 0;
    //         const rowQuantity = parseFloat($(this).find('.quantity').val()) || 0;
    //         const rowDiscount = parseFloat($(this).find('.discount').val()) || 0;
    //         const rowTaxRate = parseFloat($(this).find('.tax').val()) || 0;
    //         totalTax += (rowUnitCost * rowQuantity - rowDiscount) * (rowTaxRate / 100);
    //     });
        
    //     const orderDiscount = parseFloat($('#order_discount').val()) || 0;
    //     const shipping = parseFloat($('#shipping').val()) || 0;
        
    //     const grandTotal = subtotal + shipping - Math.min(orderDiscount, subtotal);
        
    //     // Update summary
    //     $('#total_items').text(totalItems);
    //     $('#subtotal').text(subtotal.toFixed(2));
    //     $('#total_tax').text(totalTax.toFixed(2));
    //     $('#order_discount_total').text(Math.min(orderDiscount, subtotal).toFixed(2));
    //     $('#shipping_total').text(shipping.toFixed(2));
    //     $('#grand_total').text(grandTotal.toFixed(2));
    // }

    // // Update row numbering
    // function updateRowNumbers() {
    //     $('#order_items_table tbody tr').not('#empty_row').each(function(index) {
    //         $(this).find('td:first').text(index + 1);
    //     });
    // }

    // // Calculate totals when order-level fields change
    // $('#order_discount, #shipping').on('change', calculateTotals);

    // $('#supplier').on('select2:open', function() {
    //     let $search = $('.select2-container--open .select2-search__field');
    //     $search.off('input').on('input', function() {
    //         $('#newSupplierText').text($(this).val());
    //     });
    // });
    // Handle new inline entry creation - Modified this function
    // $(document).on('click', '.add-inline-btn', function () {
    //             let attributeID = $(this).data('id');
    //             let url = $(this).data('url');
    //             let loadCallbackName = $(this).data('callback');
    //             let newValue = $('.select2-container--open .select2-search__field').val();

    //             if (!newValue) return;

    //             $.ajax({
    //                 url: url,
    //                 method: 'POST',
    //                 data: {
    //                     _token: '{{ csrf_token() }}',
    //                     name: newValue,
    //                     status: 'on'
    //                 },
    //                 success: function (response) {
    //                     console.log(response);
    //                     let $select = $('#' + attributeID);
    //                     $select.append(new Option(response.data.name, response.data.id, true, true));
    //                     $select.trigger('change');
    //                     $select.select2('close');
    //                     if (typeof window[loadCallbackName] === 'function') {
    //                         window[loadCallbackName](response.id);
    //                     }
    //                 toastr.success(`${attributeID.charAt(0).toUpperCase() + attributeID.slice(1)} added successfully`);

    //                 },
    //                 error: function(xhr) {
    //                     if (xhr.status === 422) {
    //                         toastr.error(xhr.responseJSON.error.join('<br>'));
    //                     } else {
    //                         toastr.error(`Failed to create ${attributeID}`);
    //                     }
    //                 }
    //             });
    //         });
            // $(document).on('input', '.select2-search__field', function () {
            //     let val = $(this).val();
            //     $('.add-inline-btn .new-entry-text').text(val);
            // });

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
});

</script>


@endsection

