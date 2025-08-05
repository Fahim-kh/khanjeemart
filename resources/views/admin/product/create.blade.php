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
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('product.index') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                    <iconify-icon icon="solar:arrow-left-outline" class="me-1"></iconify-icon>
                    Back
                </a>
                <h6 class="fw-semibold mb-0">Create Product</h6>
            </div>
            
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('product.create') }}" class="d-flex align-items-center gap-1 hover-text-primary">
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
                    <form id="productForm" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-body">
                                @include('admin.layouts.validationLayout')
                                <div class="row">
                                    <!-- Code Product -->
                                    <div class="col-md-6 mb-3">
                                        <label for="code" class="form-label">Code Product *</label>
                                        <div class="input-group barcode_group">
                                            <button type="button" class="input-group-text" id="scanBarcodeBtn"
                                                data-bs-toggle="modal" data-bs-target="#barcodeScanModal">
                                                <img src="{{ asset('admin/assets/images/scan.png') }}" alt="Barcode"
                                                    style="height: 20px;">
                                            </button>
                                            <input type="text" class="form-control" id="code" name="bar_code"
                                                placeholder="Scan or enter code"  autofocus>

                                            <!-- Right icon -->
                                            <button type="button" class="input-group-text" id="generateCodeBtn"
                                                title="generateCodeBtn">
                                                <img src="{{ asset('admin/assets/images/barcode.png') }}"
                                                    alt="generateCodeBtn" style="height: 20px;">
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Scan your barcode and select the correct
                                            symbology below</small>
                                    </div>
                                    <!-- Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter Name Product" >
                                    </div>

                                    <!-- Product Image -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product_image" class="form-label">Product Image</label>
                                        <input class="form-control" type="file" id="product_image" name="product_image">
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category *</label>
                                        <select class="form-select category" id="category" name="category_id">
                                            <!-- Dynamic options -->
                                        </select>
                                    </div>

                                    <!-- Brand -->
                                    <div class="col-md-6 mb-3">
                                        <label for="brand" class="form-label">Brand</label>
                                        <select class="form-select brand" id="brand" name="brand_id">
                                            <!-- Brands options dynamically loaded -->
                                        </select>
                                    </div>
                                    <!-- Brand -->

                                    <div class="col-md-6 mb-3">
                                        <label for="unit" class="form-label">Product Unit</label>
                                        <select class="form-select unit" id="unit" name="unit_id">
                                            <!-- Units options dynamically loaded -->
                                        </select>
                                    </div>

                                    <!-- Product Type -->
                                    <div class="col-md-6 mb-3">
                                        <label for="product_type" class="form-label">Product Type</label>
                                        <select class="form-select" id="product_type" name="type" >
                                            <option value="simple">Simple Product</option>
                                        </select>
                                    </div>

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
                                <div class="col-12 mb-3">
                                    @include('admin.layouts.status')
                                </div>
                                <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                <a href="{{ route('product.index') }}" class="btn btn-secondary">Close</a>
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
        $(document).ready(function () {
            loadCategories();
            loadBrands();
            loadUnits();

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

            function loadCategories(selectedId = null) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('loadCategories') }}",
                    success: function (response) {
                        let $select = $('#category');
                        $select.empty().append('<option disabled selected>Choose Category</option>');
                        response.forEach(function (item) {
                            let selected = selectedId == item.id ? 'selected' : '';
                            $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
                        });
                        $select.attr('data-url', '{{ route('category.store') }}').attr('data-callback', 'loadCategories');
                        initSelect2('category', 'Select category', '{{ route('category.store') }}', 'loadCategories');
                        if (selectedId) $select.val(selectedId).trigger('change');
                    }
                });
            }

            function loadBrands(selectedId = null) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('loadBrands') }}",
                    success: function (response) {
                        let $select = $('#brand');
                        $select.empty().append('<option disabled selected>Choose Brand</option>');
                        response.forEach(function (item) {
                            let selected = selectedId == item.id ? 'selected' : '';
                            $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
                        });
                        $select.attr('data-url', '{{ route('brand.store') }}').attr('data-callback', 'loadBrands');
                        initSelect2('brand', 'Select brand', '{{ route('brand.store') }}', 'loadBrands');
                        if (selectedId) $select.val(selectedId).trigger('change');
                    }
                });
            }

            function loadUnits(selectedId = null) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('loadUnits') }}",
                    success: function (response) {
                        let $select = $('#unit');
                        $select.empty().append('<option disabled selected>Choose Unit</option>');
                        response.forEach(function (item) {
                            let selected = selectedId == item.id ? 'selected' : '';
                            $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
                        });
                        $select.attr('data-url', '{{ route('unit.store') }}').attr('data-callback', 'loadUnits');
                        initSelect2('unit', 'Select unit', '{{ route('unit.store') }}', 'loadUnits');
                        if (selectedId) $select.val(selectedId).trigger('change');
                    }
                });
            }

            // Handle new inline entry creation - Modified this function
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

            // Generate random 8-digit code
            $('#generateCodeBtn').on('click', function () {
                $('#code').val(Math.floor(10000000 + Math.random() * 90000000));
            });

            $('#code').on('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let scannedCode = $(this).val();
                    console.log("Scanned:", scannedCode);
                }
            });
            // Product form submission with validation
            $('#productForm').on('submit', function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ route('product.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#btnSave').prop('disabled', true).text('Saving...');
                        $('.validation-error').remove();
                        $('.is-invalid').removeClass('is-invalid');
                    },
                    success: function (response) {
                        $('#btnSave').prop('disabled', false).text('Save');
                        toastr.success(response.message || "Product saved successfully");
                        $('#productForm')[0].reset();
                        $('select').val(null).trigger('change');
                        setTimeout(function() {
                            window.location.href = "{{ route('product.index') }}";
                        }, 1500);
                    },
                    error: function (xhr) {
                        $('#btnSave').prop('disabled', false).text('Save');
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function (key, value) {
                                let field = $('[name="' + key + '"]');
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    field.after(`<span class="text-danger validation-error">${value[0]}</span>`);
                                } else if (key === 'category_id') {
                                    $('#category').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                    $('#category').parent().append(`<span class="text-danger validation-error">${value[0]}</span>`);
                                }
                            });
                        } else {
                            toastr.error("Something went wrong!");
                        }
                    }
                });
            });
        });
    </script>
@endsection
