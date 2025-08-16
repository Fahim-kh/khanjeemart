@extends('admin.layouts.master')

@section('page-title')
    Purchase Return
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
                    <li class="breadcrumb-item active" aria-current="page">Product Purchase Return</li>
                </ol>
            </nav>
            <h4 class="fw-semibold mb-0">Create Purchase Return</h4>
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
                        <form id="purchaseReturnForm">
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <label for="date" class="form-label required">Date</label>
                                    <input type="date" class="form-control flatpickr-date" id="date" name="date" required value="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label">Reference No.</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="PR-{{ now()->format('YmdHis') }}" readonly>
                                    <input type="hidden" class="form-control purchase_id" id="purchase_id" name="purchase_id" value="{{$id}}" >
                                            
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label required">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="complete">Complete</option>
                                        <option value="pending">Pending</option>
                                        {{-- <option value="ordered">Ordered</option> --}}
                                    </select>
                                </div>
                            </div>

                            

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Code / Name</th>
                                        <th>Net Unit Cost</th>
                                        <th>Purchased Qty</th>
                                        <th>Stock Qty</th>
                                        <th>Return Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="returnItemsTable">
                                    <tr>
                                        <td colspan="9" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row justify-content-end mt-4">
                                <div class="col-md-4">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Order Tax</th>
                                            <td><input type="number" name="order_tax" value="0" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td><input type="number" name="discount" value="0" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <th>Shipping</th>
                                            <td><input type="number" name="shipping" value="0" class="form-control"></td>
                                        </tr>
                                        <tr class="table-light">
                                            <th>Grand Total</th>
                                            <td><strong id="grandTotal">PKR 0.00</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Please provide Comments</label>
                                <textarea name="note" rows="3" class="form-control"></textarea>
                            </div>
                            <button type="button" class="btn btn-primary " id="btnPurchaseReturn">
                                <i class="bi bi-plus-circle me-2"></i> Add Purchase Return
                            </button>
                           
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
    const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
</script>
<script src="{{ asset('admin/myjs/purchase/purchase_return.js') }}"></script>
<script>
$(document).ready(function() {
    

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


   
    function loadPurchaseItems(purchaseId) {
        let url = "{{ route('purchaseReturnItems', ':id') }}";
        url = url.replace(':id', purchaseId);

        $.get(url, function(items) {
            let tbody = $('#returnItemsTable');
            tbody.empty();

            if (items.length === 0) {
                tbody.html(`<tr><td colspan="9" class="text-center">No items found</td></tr>`);
                return;
            }

            $.each(items, function(index, item) {
                tbody.append(`
                    <tr data-cost="${item.net_unit_cost}">
                        <td>${item.row_no}</td>
                        <td>
                            <p>${item.barcode}<br>
                            <span class="badge bg-success">${item.product_name}</span></p>
                        </td>
                        <td>${item.net_unit_cost}</td>
                        <td><span class="badge bg-warning">${item.qty_purchased} pc</span></td>
                        <td><span class="badge bg-warning">${item.stock_qty} pc</span></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <button type="button" class="btn btn-outline-secondary minus-btn">-</button>
                                <input type="number" name="qty_return[]" value="0" min="0" max="${item.stock_qty}" class="form-control text-center return-qty">
                                <button type="button" class="btn btn-outline-secondary plus-btn">+</button>
                            </div>
                        </td>
                        <td class="subtotal">0.00</td>
                    </tr>
                `);
            });

            calculateGrandTotal(); // Initial calculation
        });
    }

    // Plus / Minus buttons
    $(document).on('click', '.minus-btn', function() {
        let input = $(this).siblings('.return-qty');
        let value = parseInt(input.val());
        if (value > 0) input.val(value - 1).trigger('change');
    });

    $(document).on('click', '.plus-btn', function() {
        let input = $(this).siblings('.return-qty');
        let value = parseInt(input.val());
        let max = parseInt(input.attr('max'));
        if (value < max) input.val(value + 1).trigger('change');
    });

    // Qty change event
    $(document).on('change', '.return-qty', function() {
        let row = $(this).closest('tr');
        let cost = parseFloat(row.data('cost'));
        let qty = parseInt($(this).val()) || 0;
        let subtotal = cost * qty;
        row.find('.subtotal').text(`PKR ${subtotal.toFixed(2)}`);
        calculateGrandTotal();
    });

    // Qty change event (keyboard + buttons dono ke liye)
    $(document).on('input change', '.return-qty', function() {
        let row = $(this).closest('tr');
        let cost = parseFloat(row.data('cost'));
        let qty = parseInt($(this).val()) || 0;

        let min = parseInt($(this).attr('min')) || 0;
        let max = parseInt($(this).attr('max')) || 0;

        // Validation: qty min se kam aur max se zyada na ho
        if (qty < min) {
            showToastDanger(`Minimum quantity allowed is ${min}`);
            qty = min;
        }
        if (qty > max) {
            showToastDanger(`Maximum quantity allowed is ${max}`);
            qty = max;
        }

        $(this).val(qty); // correct value wapas input me set kar do

        let subtotal = cost * qty;
        row.find('.subtotal').text(`PKR ${subtotal.toFixed(2)}`);
        calculateGrandTotal();
    });

    // Grand total calculation
    function calculateGrandTotal() {
        let total = 0;

        $('#returnItemsTable tr').each(function() {
            let subtotalText = $(this).find('.subtotal').text().replace('PKR', '').trim();
            let subtotal = parseFloat(subtotalText) || 0;
            total += subtotal;
        });

        let orderTax = parseFloat($('input[name="order_tax"]').val()) || 0;
        let discount = parseFloat($('input[name="discount"]').val()) || 0;
        let shipping = parseFloat($('input[name="shipping"]').val()) || 0;

        let calcTax = total *  orderTax / 100;

        let grandTotal = total + calcTax + shipping - discount;

        $('#grandTotal').text(`PKR ${grandTotal.toFixed(2)}`);
    }

    // Tax, Discount, Shipping change pe recalc
    $(document).on('input', 'input[name="order_tax"], input[name="discount"], input[name="shipping"]', function() {
        calculateGrandTotal();
    });

    // Page load pe load items
    let purchaseId = "{{ $id ?? '' }}";
    if (purchaseId) {
        loadPurchaseItems(purchaseId);
    }

});

</script>


@endsection

