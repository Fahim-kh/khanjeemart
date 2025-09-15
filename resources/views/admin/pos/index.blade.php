<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Font Awesome 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <style>
       :root {
            /* Light mode colors */
            --bg-color: #f1f3f6;
            --header-bg: #fff;
            --header-border: #e3e6ea;
            --text-color: #212529;
            --card-bg: #fff;
            --card-border: #eee;
            --table-header: #f8f9fa;
        }

        body.dark-mode {
            /* Dark mode overrides */
            --bg-color: #1e1e2f;
            --header-bg: #2c2c3e;
            --header-border: #3a3a50;
            --text-color: #f1f1f1;
            --card-bg: #2a2a3b;
            --card-border: #3c3c50;
            --table-header: #3a3a50;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
        }

        .pos-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
        }

        .cart-section, .product-card {
            background: var(--card-bg);
            border-color: var(--card-border);
        }

        .table thead {
            background: var(--table-header);
        }

        .total-box {
            background: #0d6efd; /* keep brand color */
            color: white;
        }

        /* Switch button */
        .mode-toggle {
            border-radius: 25px;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        /* Header */
        .pos-header {
            background: #fff;
            padding: 12px 25px;
            border-bottom: 1px solid #e3e6ea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pos-header .icon {
            margin: 0 10px;
            font-size: 1.3rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .pos-header .icon:hover {
            color: #0d6efd;
        }

        .pos-logo {
            height: 40px;
        }

        /* Main POS Layout */
        .pos-container {
            display: flex;
            height: calc(100% - 65px);
        }

        /* Cart Section */
        .cart-section {
            width: 60%;
            background: var(--card-bg);
            border-right: 1px solid var(--card-border);
            padding: 20px;
            display: flex;
            flex-direction: column;
            color: var(--text-color);
        }

        .cart-section .table {
            margin-top: 10px;
        }

        .cart-middle {
            flex-grow: 1;
            overflow-y: auto;
            padding-bottom: 15px;
        }

        .total-box {
            background: #0d6efd;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 5px;
            text-align: center;
        }

        .btn-action {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 500;
        }

        /* Product Section */
        .product-section {
            width: 40%;
            padding: 20px;
            overflow-y: auto;
        }

        .product-card {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            background: #fff;
            transition: 0.3s;
            height: 100%;
            position: relative;
        }

        .product-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .product-card img {
            max-width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .badge-stock {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #0d6efd;
            color: #fff;
            font-size: 0.7rem;
            border-radius: 5px;
            padding: 3px 6px;
        }

        .product-price {
            display: inline-block;
            background: #6f42c1;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .select2-selection,
        .select2-selection--single,
        .select2-selection--clearable {
            padding: 8px !important;
            height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px !important;
        }
        .text-start{
            font-weight: 200;
            font-size: 14px;
        }
        .icon-style{   
            border: 1px solid #ccc;
            border-radius: 50%;
            color: #ada8adcc;
        }
        .total-box {
            background: #0d6efd; /* stays brand blue */
            color: #fff;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 5px;
            text-align: center;
        }
        body.dark-mode .total-box {
            background: #0056d2; /* darker shade of blue in dark mode */
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="pos-header">
        <div>
            <span class="icon"><a href="{{ route('dashboard') }}" class="btn btn-danger" style="border-radius: 25px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a></span>
            <span class="icon" id="fullscreen-toggle"><i class="fas fa-expand"></i></span>
            <span class="icon"><button class="today_sale icon-style btn" ><i class="fas fa-file-invoice-dollar"></i></button></span>
            <span class="icon"><button class="pos_setting icon-style btn" ><i class="fas fa-cog"></i></button></span>
            <button id="mode-toggle" class="btn btn-sm btn-outline-primary mode-toggle">
                🌙 Dark
            </button>
        </div>
        <div>
            <img src="{{ asset('') }}admin/assets/images/khanjee_logo.png" class="pos-logo" alt="logo">
        </div>
    </div>

    <!-- Main POS Layout -->
    <div class="pos-container">
        <input type="hidden" name="sale_id" id="sale_id" value="999">

        <!-- Cart Section -->
        <div class="cart-section">

            <!-- Customer Dropdown -->
            <div class="mb-3">
                <select class="form-select customer" id="customer_id" name="customer_id">
                </select>
                <input type="hidden" name="customer_id_hidden" id="customer_id_hidden" >
            </div>

            <!-- Middle Scrollable Area -->
            <div class="cart-middle">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 55%; text-align: left;">Product</th>
                                <th style="width: 15%; text-align: center;">Price</th>
                                <th style="width: 15%; text-align: center;">Qty</th>
                                <th style="width: 15%; text-align: center;">Subtotal</th>
                                <th style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="showdata">
                          
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <div class="row my-3">
                    <div class="col">
                        <label>Tax</label>
                        <input type="number" class="form-control" value="0">
                    </div>
                    <div class="col">
                        <label>Discount</label>
                        <input type="number" class="form-control" value="0">
                    </div>
                    <div class="col">
                        <label>Shipping</label>
                        <input type="number" class="form-control" value="0">
                    </div>
                </div>
                <div class="total-box">Total Payable : {{ env('CURRENCY_SYMBLE') }} 0.00</div>
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-danger btn-action">Reset</button>
                    <button class="btn btn-success btn-action">Pay Now</button>
                    <button class="btn btn-primary btn-action">Draft</button>
                    <button class="btn btn-secondary btn-action">Recent Drafts</button>
                </div>
            </div>
        </div>

        <!-- Product Section -->
        <div class="product-section">
            <div class="input-group mb-3">
                <span class="input-group-text">📷</span>
                <input type="text" class="form-control product_search" id="product_search" name="product_search" placeholder="Scan/Search Product by Code Or Name">
            </div>
            <div id="searchResults" class="list-group mt-2" style="display: none; max-height: 300px; overflow-y: auto;">
                <!-- Search results will appear here -->
            </div>
            <small class="form-text text-muted">Scan barcode or type to search products</small>

            <div class="row g-3">
                <!-- Product Card -->
                <div class="col-lg-3 col-md-3">
                    <div class="product-card">
                        <span class="badge-stock">50.00 kg</span>
                        <img src="https://stocky.getstocky.com/images/products/Avocat.jpg" alt="Avocat">
                        <h6>Avocat</h6>
                        <p class="text-muted mb-1">71087180</p>
                        <span class="product-price">{{ env('CURRENCY_SYMBLE') }} 15.00</span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3">
                    <div class="product-card">
                        <span class="badge-stock">34.00 kg</span>
                        <img src="https://stocky.getstocky.com/images/products/Avocat.jpg" alt="Limon">
                        <h6>Limon</h6>
                        <p class="text-muted mb-1">82747852</p>
                        <span class="product-price">{{ env('CURRENCY_SYMBLE') }} 20.00</span>
                    </div>
                </div>
                <!-- Repeat for more products -->
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">»</a></li>
                </ul>
            </nav>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script src="{{ asset('admin/myjs/mylib.js') }}"></script>
    <script src="{{ asset('admin/myjs/pos/pos.js') }}"></script>
    <script>
        const baseUrl = "{{ env('APP_URL') }}";
        const customer_store = "{{ route('customer.store') }}";
        const pos_getSaleView = "{{ route('pos_getSaleView') }}";
        const load_customers = "{{ route('loadCustomers') }}"; 
        const product_search = "{{ route('product_search_for_sale') }}";
        const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
        const posStoreSale = "{{ route('posStoreSale') }}";

        // var token = '{{ csrf_token() }}';
    
        // --- Init Select2 with custom "Add" button ---
        function initSelect2(attributeID, placeholder, storeUrl, reloadCallback) {
            $('#' + attributeID).select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
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
                escapeMarkup: function (markup) { return markup; }
            });
        }
    
        // --- Load customers into dropdown ---
        function loadCustomers(selectedId = null, selectedName = null) {
            return $.ajax({
                type: "GET",
                url: load_customers,
                success: function (response) {
                    let walkInId = 0;
                    let $select = $('#customer_id');
                    let found = false;
                    response.forEach(function (item) {
                        const selected = (walkInId == item.id) ? 'selected' : '';
                        const displayName = item.owner == 1 ? `${item.name} (Owner)` : item.name;
                        if (selected) found = true;
                        $select.append(
                            `<option value="${item.id}" ${selected} data-isOwner="${item.owner}">
                                ${displayName}
                            </option>`
                        );
                    });
    
                    // set attributes for inline add
                    $select.attr('data-url', customer_store)
                           .attr('data-callback', 'loadCustomers');
    
                    // init select2
                    initSelect2('customer_id', 'Select Customer', customer_store, 'loadCustomers');
    
                    // handle pre-selected customer
                    if (selectedId) {
                        if (!found && selectedName) {
                            const opt = new Option(selectedName, selectedId, true, true);
                            $select.append(opt).trigger('change');
                        } else {
                            $select.val(selectedId).trigger('change');
                        }
                    }
                }
            });
        }
    
        // --- update global isOwner on selection ---
        $(document).on('change', '#customer_id', function () {
            window.isOwner = $(this).find(':selected').data('isowner'); 
        });
    
        // --- inline add button handler ---
        $(document).on('click', '.add-inline-btn', function () {
            const attributeID = $(this).data('id');
            const url = $(this).data('url');
            const reloadCallbackName = $(this).data('callback');
            const newValue = $('.select2-container--open .select2-search__field').val();
            if (!newValue) return;
    
            $.post(url, {
                _token: token,
                name: newValue,
                status: 'on'
            }).done(function (response) {
                const $select = $('#' + attributeID);
                $select.append(new Option(response.data.name, response.data.id, true, true))
                       .trigger('change')
                       .select2('close');
    
                // FIX: Proper callback call
                if (typeof window[reloadCallbackName] === 'function') {
                    window[reloadCallbackName](response.data.id, response.data.name);
                }
                toastr.success(`${attributeID.charAt(0).toUpperCase() + attributeID.slice(1)} added successfully`);
            }).fail(function (xhr) {
                if (xhr.status === 422) {
                    toastr.error((xhr.responseJSON.error || []).join('<br>'));
                } else {
                    toastr.error(`Failed to create ${attributeID}`);
                }
            });
        });
    
        // --- update inline button text as user types ---
        $(document).on('input', '.select2-search__field', function () {
            $('.add-inline-btn .new-entry-text').text($(this).val());
        });
    
        // --- Init on page load ---
        $(document).ready(function () {
            loadCustomers(); // load customers into dropdown
        });
        $(document).ready(function () {
            loadCustomers();

            // Dark/Light mode toggle
            const toggleBtn = document.getElementById('mode-toggle');
            const body = document.body;

            // Check saved mode
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                toggleBtn.textContent = "☀️ Light";
                toggleBtn.classList.remove("btn-outline-primary");
                toggleBtn.classList.add("btn-outline-warning");
            }

            toggleBtn.addEventListener('click', function () {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    toggleBtn.textContent = "☀️ Light";
                    toggleBtn.classList.remove("btn-outline-primary");
                    toggleBtn.classList.add("btn-outline-warning");
                    localStorage.setItem('theme', 'dark');
                } else {
                    toggleBtn.textContent = "🌙 Dark";
                    toggleBtn.classList.remove("btn-outline-warning");
                    toggleBtn.classList.add("btn-outline-primary");
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>
    
</html>
