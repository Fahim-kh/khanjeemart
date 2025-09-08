@extends('admin.layouts.master')

@section('page-title')
     Purchase Return List
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Purchase Return List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('purchase.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Purchase Return List
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')
            <div class="row justify-content-center ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                             <!-- <button type="button" id="delete_all_record" url="product/deleteAll"
                    class="btn btn-danger delete_all">Delete</button> -->

                            <!-- @if (isset(Auth::user()->hasPer('Purchase Return')['pcreate']) && Auth::user()->hasPer('Purchase Return')['pcreate'] == 1)
                                <a href="{{ route('purchase.create') }}" class="btn btn-success">Add New Purchase</a>
                            @endif -->
                        </div>
                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-start">S No</th>
                                        <th scope="col" class="text-start">Date</th>
                                        <th scope="col">Reference</th>
                                        <th scope="col">Supplier</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-start">Grand Total</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                 </thead>
                            </table>
                        </div>
                        
                        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        Do you want to delete this record?
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <div class="modal fade" id="purchaseReturnDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Return Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Top Info -->
                    <table class="table table-bordered mb-3">
                        <tbody>
                            <tr>
                                <th>Date</th>
                                <td id="ret_date"></td>
                                <th>Purchase Invoice</th>
                                <td id="ret_reference"></td>
                            </tr>
                            <tr>
                                <th>Supplier</th>
                                <td id="ret_supplier"></td>
                                <th>Purchase Ret Invoice</th>
                                <td id="ret_invoice"></td>
                            </tr>
                            <tr>
                                    <th>Total Amount</th>
                                    <td id="ret_total_amount"></td>
                                    <th>Discount</th>
                                    <td id="ret_discount"></td>
                                </tr>
                                <tr>
                                    <th>Tax</th>
                                    <td id="ret_tax"></td>
                                    <th>Shipping Charge</th>
                                    <td id="ret_shipping"></td>
                                </tr>
                                <tr class="table-success">
                                    <th>Grand Total</th>
                                    <td colspan="3" id="ret_grand_total"></td>
                                </tr>
                        </tbody>
                    </table>

                    <!-- Items -->
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Product Code</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="ret_items">
                            <!-- JS se rows inject hongi -->
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>

            </div>
        </div>
    </div>

    </div>
@endsection

@section('script')

<script>
    const baseUrl = "{{ env('APP_URL') }}";
</script>

<script src="{{ asset('admin/myjs/purchase_return/purchase_return.js') }}"></script>
@endsection
