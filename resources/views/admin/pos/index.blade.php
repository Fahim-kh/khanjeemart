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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Font Awesome 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="pos_print.css"> --}}
    {{-- <style id="__web-inspector-hide-shortcut-style__">
        .__web-inspector-hide-shortcut__,
        .__web-inspector-hide-shortcut__ * {
            visibility: hidden !important;
        }
    </style> --}}
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

        body,
        html {
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

        .cart-section,
        .product-card {
            background: var(--card-bg);
            border-color: var(--card-border);
        }

        .table thead {
            background: var(--table-header);
        }

        .total-box {
            background: #0d6efd;
            /* keep brand color */
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

        .text-start {
            font-weight: 200;
            font-size: 14px;
        }

        .icon-style {
            border: 1px solid #ccc;
            border-radius: 50%;
            color: #ada8adcc;
        }

        .total-box {
            background: #0d6efd;
            /* stays brand blue */
            color: #fff;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 5px;
            text-align: center;
        }

        body.dark-mode .total-box {
            background: #0056d2;
            /* darker shade of blue in dark mode */
        }

        /* Hide number input arrows */
        .no-spinner::-webkit-outer-spin-button,
        .no-spinner::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .no-spinner {
            -moz-appearance: textfield;
            /* Firefox */
        }
        .product_search{
            padding:10px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="pos-header">
        <div>
            <span class="icon"><a href="{{ route('dashboard') }}" class="btn btn-danger"
                    style="border-radius: 25px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a></span>
            <span class="icon" id="fullscreen-toggle"><i class="fas fa-expand"></i></span>
            <span class="icon"><button class="today_sale icon-style btn " id="todaySaleBtn"><i
                        class="fas fa-file-invoice-dollar"></i></button></span>
            {{-- <span class="icon"><button class="pos_setting icon-style btn"><i class="fas fa-cog"></i></button></span> --}}
            <button id="mode-toggle" class="btn btn-sm btn-outline-primary mode-toggle">
                🌙 Dark
            </button>
        </div>
        <div>
            <img src="{{ asset('') }}admin/assets/images/khanjee_logo.png" class="pos-logo" alt="logo">
        </div>
    </div>

    <!-- Main POS Layout -->
    <form id="posForm">
        @csrf
        <input type="hidden" name="sale_id" id="sale_id" value="999">
        @php
            $randomNumber = rand(1000, 1999);
        @endphp
        <input type="hidden" class="form-control" id="reference" name="reference" value="PS_{{ $randomNumber }}"
            readonly>
        <input type="hidden" name="customer_id_hidden" id="customer_id_hidden">
        <input type="hidden" name="sale_date" value="{{ now()->format('Y-m-d') }}">


    </form>
    <div class="pos-container">

        <!-- Cart Section -->
        <div class="cart-section">

            <!-- Customer Dropdown -->
            <div class="mb-3">
                <select class="form-select customer" id="customer_id" name="customer_id">
                </select>
                {{-- <input type="hidden" name="customer_id_hidden" id="customer_id_hidden" > --}}

            </div>

            <!-- Middle Scrollable Area -->
            <div class="cart-middle">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 55%; text-align: left;">Product</th>
                                <th style="width: 15%; text-align: center;">Price</th>
                                <th style="width: 15%; text-align: center;">Stock</th>
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
                <form id="posForm">
                    <div class="row my-3">
                        <div class="col d-none">
                            <label>Tax</label>
                            <input type="number" id="order_tax" name="order_tax" class="form-control order_tax"
                                value="0">
                        </div>
                        <div class="col">
                            <label>Extra Items Amount</label>
                            <input type="number" id="extra_amount" name="extra_amount"
                                class="form-control extra_amount" value="0">
                        </div>
                        <div class="col">
                            <label>Discount</label>
                            <input type="number" name="discount" class="form-control discount" id="discount"
                                value="0">
                        </div>
                        <div class="col">
                            <label>Shipping</label>
                            <input type="number" id="shipping" name="shipping" class="form-control" value="0">
                        </div>
                    </div>
                    <input type="hidden" id="sub_total" class="pos_total">
                    <input type="hidden" name="status" value="complete">
                </form>
                <div class="total-box">Total Payable : {{ env('CURRENCY_SYMBLE') }}
                    <span class="grand_total" id="grand_total">0.00</span>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-danger btn-action" id="btnReset">Reset</button>
                    <button class="btn btn-success btn-action modalbtnFinalSave enterButtonActive" id="modalbtnFinalSave">Pay
                        Now</button>
                    <button class="btn btn-primary btn-action btnFinalDraft">Draft</button>
                    <button class="btn btn-secondary btn-action recentDraft">Recent Drafts</button>
                </div>
            </div>
        </div>

        <!-- Product Section -->
        <div class="product-section">
            <div class="input-group mb-3">
                <span class="input-group-text">📷</span>
                <input type="text" class="form-control product_search" id="product_search" name="product_search"
                    placeholder="Scan/Search Product by Code Or Name"  autocomplete="off">
            </div>
            <div id="searchResults" class="list-group mt-2"
                style="display: none; max-height: 300px; overflow-y: auto;">
                <!-- Search results will appear here -->
            </div>
            <small class="form-text text-muted">Scan barcode or type to search products</small>

            <div class="row g-3" id="product-list"></div>

            <nav class="mt-4">
                <ul class="pagination justify-content-center" id="pagination-links">
                </ul>
            </nav>
        </div>
    </div>

    @include('admin.layouts.lastSalePurchaseDialog')
    @include('admin.layouts.delete')
    @include('admin.layouts.posPayModal')
    @include('admin.layouts.printView');
    @include('admin.layouts.posTodaySaleSummery');
    @include('admin.layouts.posDraftSummeryModal');
    <!-- Draft Summary Modal -->
  
    {{-- payment --}}

    <!-- Payment Modal -->



    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>


    <script src="{{ asset('admin/myjs/mylib.js') }}"></script>
    <script src="{{ asset('admin/myjs/pos/pos.js') }}"></script>
    <script>
        const baseUrl = "{{ env('APP_URL') }}";
        const getSaleIndexUrl = "{{ route('sale.index') }}";
        const customer_store = "{{ route('customer.store') }}";
        const pos_getSaleView = "{{ route('pos_getSaleView') }}";
        const load_customers = "{{ route('loadCustomers') }}";
        const product_search = "{{ route('product_search_for_sale') }}";
        const latestPosProducts = "{{ route('latestPosProducts') }}";
        const imageUrl = "{{ env('APP_URL') }}/admin/uploads/products";
        const posStoreSale = "{{ route('posStoreSale') }}";
        const posUpdateSaleItem = "{{ route('posUpdateSaleItem') }}";
        const getPurchaseIndexUrl = "{{ route('purchase.index') }}";
        const storeFinalSale = "{{ route('storeFinalSale') }}";
        const storeFinalSaleDraft = "{{ route('posStoreFinalSaleDraft') }}";
        const sale_print = "{{ route('sale.print', ':id') }}";
        const posDraftSaleDetail = "{{ route('posDraftSaleDetail', ':id') }}";
        const pos_draft_summery = "{{ route('pos_draft_summery') }}";
        const posTodaySaleSummery = "{{ route('posTodaySaleSummery') }}";



        // var token = '{{ csrf_token() }}';

        // --- Init Select2 with custom "Add" button ---
        function initSelect2(attributeID, placeholder, storeUrl, reloadCallback) {
            $('#' + attributeID).select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                language: {
                    noResults: function() {
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
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        }

        // --- Load customers into dropdown ---
        function loadCustomers(selectedId = null, selectedName = null) {
            return $.ajax({
                type: "GET",
                url: load_customers,
                success: function(response) {
                    let walkInId = 0;
                    let $select = $('#customer_id');
                    let found = false;
                    response.forEach(function(item) {
                        const selected = (walkInId == item.id) ? 'selected' : '';
                        const displayName = item.owner == 1 ? `${item.name} (Owner)` : item.name;
                        if (selected) found = true;
                        $select.append(
                            `<option value="${item.id}" ${selected} data-isOwner="${item.owner}" data-customerName="${item.name}">
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
                            // $select.val(selectedId).trigger('change');
                        }
                    }
                    let selectedText = $select.find("option:selected").text();
                    $(".customername").text(selectedText);
                }
            });
        }

        // --- update global isOwner on selection ---
        $(document).on('change', '#customer_id', function() {
            let customerId = $(this).val();
            console.log(customerId);
            window.isOwner = $(this).find(':selected').data('isowner');
            window.customerName = $(this).find(':selected').data('customername');
            $('#customer_id_hidden').val(customerId);
            $(".customername").text(window.customerName);
            $(".customerName").text(window.customerName);
        });

        // --- inline add button handler ---
        $(document).on('click', '.add-inline-btn', function() {
            const attributeID = $(this).data('id');
            const url = $(this).data('url');
            const reloadCallbackName = $(this).data('callback');
            const newValue = $('.select2-container--open .select2-search__field').val();
            if (!newValue) return;

            $.post(url, {
                _token: token,
                name: newValue,
                status: 'on'
            }).done(function(response) {
                const $select = $('#' + attributeID);
                $select.append(new Option(response.data.name, response.data.id, true, true))
                    .trigger('change')
                    .select2('close');

                // FIX: Proper callback call
                if (typeof window[reloadCallbackName] === 'function') {
                    window[reloadCallbackName](response.data.id, response.data.name);
                }
                toastr.success(
                    `${attributeID.charAt(0).toUpperCase() + attributeID.slice(1)} added successfully`);
            }).fail(function(xhr) {
                if (xhr.status === 422) {
                    toastr.error((xhr.responseJSON.error || []).join('<br>'));
                } else {
                    toastr.error(`Failed to create ${attributeID}`);
                }
            });
        });

        // --- update inline button text as user types ---
        $(document).on('input', '.select2-search__field', function() {
            $('.add-inline-btn .new-entry-text').text($(this).val());
        });
        $(document).ready(function() {
            let customerId = localStorage.getItem("customer_id");
            if (customerId) {
                $("#customer_id_hidden").val(customerId);
                loadCustomers(customerId,null);
            } else {
                loadCustomers(); 
            }
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

            toggleBtn.addEventListener('click', function() {
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
        let enterStep = 0;
        $(document).on('keydown', function (e) {
            // Just press `/` key
            if (e.key === '/') {
                e.preventDefault();
                $('.dt-input').focus();
                $('.product_search').focus();
            }
            // if (e.key === 'Enter') {
            //     e.preventDefault();

            //     if (enterStep === 0) {
            //         $('.enterButtonActive').trigger('click'); 
            //         enterStep = 1;
            //     } 
            //     else if (enterStep === 1) {
            //         $('.enterButtonActive2').trigger('click'); 
            //         enterStep = 0; 
            //     }
            // }
            $(document).on('keydown', function (e) {
                if (e.key === "\\") {   // Backslash key
                    e.preventDefault();

                    if (enterStep === 0) {
                        $('.enterButtonActive').trigger('click'); 
                        enterStep = 1;
                    } 
                    else if (enterStep === 1) {
                        $('.enterButtonActive2').trigger('click'); 
                        enterStep = 0; 
                    }
                }
            });
            if (e.keyCode === 192 || e.key === '`') {
                e.preventDefault(); // stop typing the ` character
                $('.sell-price-input').first().focus().select();
            }

        });
        function clearSaleSession() {
            localStorage.removeItem("sale_id");
            localStorage.removeItem("customer_id");
        }
    </script>

</html>
