@extends('admin.layouts.master')

@section('page-title')
 Payments
@endsection
@section('main-content')
    <style>
        .select2-container,
        .select2-selection,
        .select2-dropdown {
            width: 760.5px !important;
        }
    </style>
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Payment List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Payments
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>

        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header">
                            @if (isset(Auth::user()->hasPer('Payments')['pcreate']) && Auth::user()->hasPer('Payments')['pcreate'] == 1)
                                <button type="button" class="btn btn-success create">Add New Payment</button>
                            @endif
                        </div>

                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th class="text-start">#</th>
                                        <th class="text-start">Date</th>
                                        <th class="text-start">Type</th>
                                        <th>Mode</th>
                                        <th class="text-start">Customer</th>
                                        <th class="text-start">Supplier</th>
                                        <th class="text-start">Amount</th>
                                        <th>Comments</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <!-- Add/Edit Modal -->
                        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content shadow-lg border-0 rounded-3">

                                    <!-- Modal Header -->
                                    <div class="modal-header  text-white">
                                        <h5 class="modal-title" id="modalAddLabel">
                                            <i class="bi bi-cash-coin me-2"></i> New Payment
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <form id="payment">
                                        @include('admin.layouts.validationLayout')

                                        <!-- Modal Body -->
                                        <div class="modal-body">
                                            <input type="hidden" name="id" class="id">

                                            <div class="row g-3">
                                                <!-- Date -->

                                                <div class="col-md-6">
                                                
                                                    <label for="date" class="form-label fw-semibold">Date</label>
                                                    <input type="date" class="form-control flatpickr-date entry_date" name="entry_date" id="entry_date"
                                                        required value="{{ now()->format('Y-m-d') }}">
                                                </div>

                                                <!-- Transaction Type -->
                                                <div class="col-md-6">
                                                    <label for="transaction_type" class="form-label fw-semibold">Transaction
                                                        Type *</label>
                                                    <select name="transaction_type" id="transaction_type"
                                                        class="form-select transaction_type" required>
                                                        <option value="">Select</option>
                                                        <option value="PaymentFromCustomer">Receive from Customer</option>
                                                        <option value="PaymentToVendor">Pay to Vendor</option>
                                                    </select>
                                                </div>

                                                <!-- Customer -->
                                                <div class="col-md-12 customer_div d-none">
                                                    <label for="customer_id" class="form-label fw-semibold">Customer</label>
                                                    <select name="customer_id" id="customer_id"
                                                        class="form-select customer_id">
                                                        <option value="">Select Customer</option>
                                                        @foreach ($customers as $cus)
                                                            <option value="{{ $cus->id }}">{{ $cus->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Supplier -->
                                                <div class="col-md-12 supplier_div d-none">
                                                    <label for="supplier_id" class="form-label fw-semibold">Supplier</label>
                                                    <select name="supplier_id" id="supplier_id"
                                                        class="form-select supplier_id">
                                                        <option value="">Select Supplier</option>
                                                        @foreach ($suppliers as $sup)
                                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Payment Method -->
                                                <div class="col-md-6">
                                                    <label for="trans_mode" class="form-label fw-semibold">Payment Method
                                                        *</label>
                                                    <select name="trans_mode" id="trans_mode" class="form-select trans_mode"
                                                        required>
                                                        <option selected value="cash">Cash</option>
                                                        <option value="cheque">Cheque</option>
                                                        <option value="bank">Bank Transfer</option>
                                                    </select>
                                                </div>

                                                <!-- Cheque Fields -->
                                                <div class="row cheque_fields d-none g-3">
                                                    <div class="col-md-6">
                                                        <label for="cheque_no" class="form-label fw-semibold">Cheque
                                                            No</label>
                                                        <input type="text" class="form-control cheque_no" name="cheque_no">
                                                    </div>
                                                    <div class="col-md-6" style="display: none;">
                                                        <label for="cheque_date" class="form-label fw-semibold">Cheque
                                                            Date</label>
                                                        <input type="date" class="form-control cheque_date" 
                                                            name="cheque_date" required value="{{ now()->format('Y-m-d') }}" >
                                                    </div>
                                                </div>

                                                <!-- Amount -->
                                                <div class="col-md-6">
                                                    <label for="amount" class="form-label fw-semibold">Amount *</label>
                                                    <input type="number" step="0.01" class="form-control amount"
                                                        name="amount" required>
                                                </div>

                                                <!-- Comments -->
                                                <div class="col-12">
                                                    <label for="comments" class="form-label fw-semibold">Comments</label>
                                                    <textarea name="comments" class="form-control comments" rows="3"
                                                        placeholder="Optional..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer bg-light">
                                            <button type="submit" id="btnSave" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i> Save
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle me-1"></i> Close
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- Delete Modal -->
                        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        Do you want to delete this record?
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div><!-- card -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const payment_store = "{{ route('payment.store') }}";
        $('#customer_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Select Customer",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#modalAdd')
        });
        $('#supplier_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Select Supplier",
            allowClear: false,
            width: '100%',
            dropdownParent: $('#modalAdd')
        });
    </script>
    <script src="{{ asset('admin/myjs/payment/payment.js') }}"></script>
@endsection