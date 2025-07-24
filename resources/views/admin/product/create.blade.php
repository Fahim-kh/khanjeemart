@extends('admin.layouts.master')

@section('page-title')
    Create Product
@endsection
@section('main-content')
    <style>
        #scanner-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
        }

        #video {
            width: 100%;
            border: 2px solid #333;
        }

        #result {
            margin-top: 20px;
            padding: 15px;
            font-size: 24px;
            text-align: center;
            background: #f0f0f0;
            border-radius: 5px;
        }

        #startButton,
        #startButton {
            padding: 10px 20px;
            font-size: 16px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Create Product</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Create Product
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
                    <form id="productForm">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.layouts.validationLayout')
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter Name Product" required>
                                    </div>

                                    <!-- Product Image -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product_image" class="form-label">Product Image</label>
                                        <input class="form-control" type="file" id="product_image" name="image">
                                    </div>

                                    <!-- Code Product -->
                                    <div class="col-md-6 mb-3">
                                        <label for="code" class="form-label">Code Product *</label>
                                        <div class="input-group">
                                            <button type="button" class="input-group-text" id="scanBarcodeBtn"
                                                data-bs-toggle="modal" data-bs-target="#barcodeScanModal">
                                                <img src="{{ asset('admin/assets/images/scan.png') }}" alt="Barcode"
                                                    style="height: 20px;">
                                            </button>
                                            <input type="text" class="form-control" id="code" name="code"
                                                placeholder="Scan or enter code" required>

                                            <!-- Right icon -->
                                            <button type="button" class="input-group-text" id="generateCodeBtn" title="generateCodeBtn">
                                                <img src="{{ asset('admin/assets/images/barcode.png') }}" alt="generateCodeBtn" style="height: 20px;">
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Scan your barcode and select the correct
                                            symbology below</small>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category *</label>
                                        <select class="form-select category" id="category" name="category">
                                            <!-- Dynamic options -->
                                        </select>
                                    </div>

                                    <!-- Brand -->
                                    <div class="col-md-6 mb-3">
                                        <label for="brand" class="form-label">Brand</label>
                                        <select class="form-select brand" id="brand" name="brand">
                                            <!-- Brands options dynamically loaded -->
                                        </select>
                                    </div>
                                    <!-- Brand -->
                                    

                                    <!-- Order Tax -->
                                    {{-- <div class="col-md-6 mb-3">
                                        <label for="order_tax" class="form-label">Order Tax</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="order_tax" name="order_tax"
                                                value="0" min="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div> --}}

                                    <!-- Tax Type -->
                                    {{-- <div class="col-md-6 mb-3">
                                        <label for="tax_type" class="form-label">Tax Type *</label>
                                        <select class="form-select" id="tax_type" name="tax_type" required>
                                            <option value="exclusive">Exclusive</option>
                                            <option value="inclusive">Inclusive</option>
                                        </select>
                                    </div> --}}

                                    <!-- Description -->
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" placeholder="A few words ..." rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {{-- <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Type *</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="standard">Standard Product</option>
                                            <option value="composite">Composite Product</option>
                                            <option value="digital">Digital Product</option>
                                        </select>
                                    </div> --}}
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Product Cost *</label>
                                        <input type="number" class="form-control" id="price" name="price"
                                            placeholder="Enter Product Price" step="any"  required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sale_price" class="form-label">Product Sale Price *</label>
                                        <input type="number" class="form-control" id="sale_price" name="sale_price"
                                            placeholder="Enter Product Sale Price" step="any" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="unit" class="form-label">Product Unit</label>
                                        <select class="form-select unit" id="unit" name="unit">
                                            <!-- Units options dynamically loaded -->
                                        </select>
                                    </div>
                                    {{-- <!-- Sale Unit -->
                                    <div class="col-md-6 mb-3">
                                        <label for="sale_unit" class="form-label">Sale Unit *</label>
                                        <select class="form-select" id="sale_unit" name="sale_unit" required>
                                            <option value="" selected disabled>Choose Sale Unit</option>
                                            <!-- Dynamic options will be loaded here -->
                                        </select>
                                    </div> --}}
                                        <!-- Stock Alert -->
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_alert" class="form-label">Stock Alert</label>
                                        <input type="number" class="form-control" id="stock_alert" name="stock_alert"
                                            value="0" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="col-12 mb-3">
                                    @include('admin.layouts.status')
                                </div>
                                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
   $(document).ready(function() {
    loadCategories();
    loadBrands();
    loadUnits();

    $('#category').on('select2:open', function () {
        let $search = $('.select2-container--open .select2-search__field');
        $search.off('input').on('input', function () {
            $('#newCatText').text($(this).val());
        });
    });
    $('#brand').on('select2:open', function () {
        let $search = $('.select2-container--open .select2-search__field');
        $search.off('input').on('input', function () {
            $('#newBrandText').text($(this).val());
        });
    });

    $(document).on('click', '#addInlineCategory', function () {
        let newCategory = $('.select2-container--open .select2-search__field').val();
        let status = 'on'
        $.ajax({
            url: '{{ route("category.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: newCategory,
                status: status,
            },
            success: function (response) {
                let newOption = new Option(response.name, response.id, true, true);
                $('#category').append(newOption).trigger('change');
                $('#category').select2('close');
                loadCategories();
            },
            error: function () {
                alert('Failed to create category.');
            }
        });
    });
    $(document).on('click', '#addInlineBrand', function () {
        let newBrand = $('.select2-container--open .select2-search__field').val();
        let status = 'on'
        $.ajax({
            url: '{{ route("brand.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: newBrand,
                status: status,
            },
            success: function (response) {
                let newOption = new Option(response.name, response.id, true, true);
                $('#brand').append(newOption).trigger('change');
                $('#brand').select2('close');
                loadBrands();
            },
            error: function () {
                alert('Failed to create brand.');
            }
        });
    });
    $(document).on('click', '#addInlineUnit', function () {
        let newUnit = $('.select2-container--open .select2-search__field').val();
        let status = 'on'
        $.ajax({
            url: '{{ route("unit.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: newUnit,
                status: status,
            },
            success: function (response) {
                let newOption = new Option(response.name, response.id, true, true);
                $('#unit').append(newOption).trigger('change');
                $('#unit').select2('close');
                loadUnits();
            },
            error: function () {
                alert('Failed to create unit.');
            }
        });
    });

    function loadCategories() {
        $.ajax({
            type: "GET",
            url: "{{ route('loadCategories') }}",
            success: function (response) {
                let $categorySelect = $('#category');
                $categorySelect.empty(); // Clear existing options

                $categorySelect.append('<option disabled selected>Choose Category</option>');
                
                response.forEach(function (category) {
                    $categorySelect.append(
                        $('<option>', {
                            value: category.id,
                            text: category.name
                        })
                    );
                });

                // Re-initialize Select2
                initCategorySelect2();
            },
            error: function () {
                alert("Failed to load categories.");
            }
        });
    } 
    function loadBrands() {
        $.ajax({
            type: "GET",
            url: "{{ route('loadBrands') }}",
            success: function (response) {
                let $brand = $('#brand');
                $brand.empty(); // Clear existing options

                $brand.append('<option disabled selected>Choose Brand</option>');
                
                response.forEach(function (brand) {
                    $brand.append(
                        $('<option>', {
                            value: brand.id,
                            text: brand.name
                        })
                    );
                });

                // Re-initialize Select2
                initBrandSelect2();
            },
            error: function () {
                alert("Failed to load brands.");
            }
        });
    } 
    function loadUnits() {
        $.ajax({
            type: "GET",
            url: "{{ route('loadUnits') }}",
            success: function (response) {
                let $unit = $('#unit');
                $unit.empty(); // Clear existing options

                $unit.append('<option disabled selected>Choose Unit</option>');
                
                response.forEach(function (unit) {
                    $unit.append(
                        $('<option>', {
                            value: unit.id,
                            text: unit.name
                        })
                    );
                });

                // Re-initialize Select2
                initUnitSelect2();
            },
            error: function () {
                alert("Failed to load units.");
            }
        });
    } 
    
    function initCategorySelect2(){
        $('#category').select2({
            width: '100%',
            placeholder: 'Choose Category',
            language: {
                noResults: function () {
                    return `<div class="text-center">
                                <em>No results found</em><br/>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="addInlineCategory">+ Add "<span id="newCatText"></span>"</button>
                            </div>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    } 
    function initBrandSelect2(){
        $('#brand').select2({
            width: '100%',
            placeholder: 'Choose Brand',
            language: {
                noResults: function () {
                    return `<div class="text-center">
                                <em>No results found</em><br/>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="addInlineBrand">+ Add "<span id="newBrandText"></span>"</button>
                            </div>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    } 
    function initUnitSelect2(){
        $('#unit').select2({
            width: '100%',
            placeholder: 'Choose Unit',
            language: {
                noResults: function () {
                    return `<div class="text-center">
                                <em>No results found</em><br/>
                                <button type="button" class="btn btn-sm btn-primary mt-2" id="addInlineUnit">+ Add "<span id="newUnitText"></span>"</button>
                            </div>`;
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }  
    $('#generateCodeBtn').on('click', function () {
        var randomCode = Math.floor(10000000 + Math.random() * 90000000); // Generate 8-digit number
        $('#code').val(randomCode); // Set it into the input field
    });
});
</script>
@endsection
