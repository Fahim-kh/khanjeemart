@extends('admin.layouts.master')

@section('page-title')
     Products List
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Products List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Products List
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

                            @if (isset(Auth::user()->hasPer('Products')['pcreate']) && Auth::user()->hasPer('Products')['pcreate'] == 1)
                                <a href="{{ route('product.create') }}" class="btn btn-success">Add New Product</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-start">S No</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                 </thead>
                            </table>
                        </div>

                        
                        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAddLabel">New Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <!-- Form -->
                                    {{-- <form id="product">
                                        @include('admin.layouts.validationLayout')
                                        
                                        <div class="modal-body">
                                            <input type="hidden" name="id" class="id">
                                            
                                            <!-- Name Input -->
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control name" name="name" id="name" placeholder="Enter Name" required>
                                            </div>
                                            
                                            @include('admin.layouts.status')
                                            
                                        </div>
                                        
                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form> --}}
                                    <div class="modal-body">
                                        <form id="productForm">
                                            @include('admin.layouts.validationLayout')
                                            <div class="row">
                                                <!-- Name -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Name *</label>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name Product" required>
                                                </div>
                                        
                                                <!-- Product Image -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="image" class="form-label">Product Image</label>
                                                    <input class="form-control" type="file" id="image" name="image">
                                                </div>
                                        
                                                <!-- Barcode Symbology -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="barcode_symbology" class="form-label">Barcode Symbology *</label>
                                                    <select class="form-select" id="barcode_symbology" name="barcode_symbology" required>
                                                        <option value="code128">Code 128</option>
                                                        <option value="code39">Code 39</option>
                                                        <option value="ean13">EAN-13</option>
                                                        <!-- Add more as needed -->
                                                    </select>
                                                </div>
                                        
                                                <!-- Code Product -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="code" class="form-label">Code Product *</label>
                                                    <div class="input-group">
                                                        <button type="button" class="input-group-text" id="scanBarcodeBtn">
                                                            <img src="{{ asset('admin/assets/images/scan.png') }}" alt="Barcode" style="height: 20px;">
                                                        </button>
                                                        <input type="text" class="form-control" id="code" name="code" placeholder="Scan or enter code" required>
                                                    </div>
                                                    <small class="form-text text-muted">Scan your barcode and select the correct symbology below</small>
                                                </div>
                                        
                                                <!-- Category -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="category" class="form-label">Category *</label>
                                                    <select class="form-select" id="category" name="category" required>
                                                        <option selected disabled>Choose Category</option>
                                                        <!-- Categories options dynamically loaded -->
                                                    </select>
                                                </div>
                                        
                                                <!-- Brand -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="brand" class="form-label">Brand</label>
                                                    <select class="form-select" id="brand" name="brand">
                                                        <option selected disabled>Choose Brand</option>
                                                        <!-- Brands options dynamically loaded -->
                                                    </select>
                                                </div>
                                        
                                                <!-- Order Tax -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="order_tax" class="form-label">Order Tax</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="order_tax" name="order_tax" value="0" min="0">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                        
                                                <!-- Tax Type -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="tax_type" class="form-label">Tax Type *</label>
                                                    <select class="form-select" id="tax_type" name="tax_type" required>
                                                        <option value="exclusive">Exclusive</option>
                                                        <option value="inclusive">Inclusive</option>
                                                    </select>
                                                </div>
                                        
                                                <!-- Description -->
                                                <div class="col-12 mb-3">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="description" name="description" placeholder="A few words ..." rows="3"></textarea>
                                                </div>

                                                <div class="col-12 mb-3">
                                                    @include('admin.layouts.status')
                                                </div>
                                            </div>
                                            
                                            <!-- Submit Buttons -->
                                            <div class="modal-footer">
                                                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
    </div>
@endsection

@section('script')
<script src="{{ asset('admin/myjs/product/product.js') }}"></script>
@endsection
