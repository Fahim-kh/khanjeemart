@extends('admin.layouts.master')

@section('page-title')
    Setup Customer Form
@endsection

@section('main-content')
<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Customer List</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Customer List
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
                        @if (isset(Auth::user()->hasPer('Customer')['pcreate']) && Auth::user()->hasPer('Customer')['pcreate'] == 1)
                            <button type="button" class="btn btn-success create">Add New Customer</button>
                        @endif
                    </div>

                    <div class="card-body">
                        <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th scope="col" class="text-start">S No</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col" class="text-start">Phone</th>
                                    <th scope="col">City</th>
                                    <th scope="col">Opening Balance</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Modal: Add/Edit Customer -->
                    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAddLabel">New Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form id="customer">
                                    @include('admin.layouts.validationLayout')

                                    <div class="modal-body">
                                        <input type="hidden" name="id" class="id">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control name" name="name" id="name" placeholder="Enter Name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control phone" name="phone" id="phone" placeholder="Enter Phone">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="country" class="form-label">Country</label>
                                                    <input type="text" class="form-control country" name="country" id="country" placeholder="Enter Country">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="tax_number" class="form-label">Tax Number</label>
                                                    <input type="text" class="form-control tax_number" name="tax_number" id="tax_number" placeholder="Enter Tax Number">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="is_owner" class="form-label">Is Owner</label>
                                                    <select name="owner" id="owner" class="form-control">
                                                        <option value="0">Customer</option>
                                                        <option value="1">Owner</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control email" name="email" id="email" placeholder="Enter Email">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control address" name="address" id="address" placeholder="Enter Address">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="city" class="form-label">City</label>
                                                    <input type="text" class="form-control city" name="city" id="city" placeholder="Enter City">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="opening_balance" class="form-label">Opening Balance</label>
                                                    <input type="number" class="form-control opening_balance" name="opening_balance" id="opening_balance" placeholder="Opening Balance">
                                                </div>
                                                

                                                <div class="mb-3">
                                                    @include('admin.layouts.status')
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Confirm Delete -->
                    <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    Do you want to delete this record?
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete Modal -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('admin/myjs/customer/customer.js') }}"></script>
@endsection
