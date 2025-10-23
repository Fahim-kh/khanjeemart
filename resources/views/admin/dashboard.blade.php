@extends('admin.layouts.master')

@section('page-title')
    Dashboard
@endsection
@section('main-content')
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Dashboard</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="row gy-4">
            <div class="col-12">
                <div class="card radius-12">
                    <div class="card-body p-16">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small>Daily*</small>
                            <button id="toggleVisibility" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-eye-off-line"></i>
                            </button>
                        </div>

                        <div id="dailyStats" class="row gy-1 stats-blurred">
                            <!-- Gross Sales -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Gross Sales</span>
                                            <h6 class="fw-semibold mb-1 sale">PKR 00,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                            <i class="ri-shopping-cart-fill"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Purchase -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Purchase</span>
                                            <h6 class="fw-semibold mb-1 purchases">PKR 00,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-lilac-200 text-lilac-600">
                                            <i class="ri-handbag-fill"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Expense -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Expense</span>
                                            <h6 class="fw-semibold mb-1 expense">PKR 00,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                            <i class="ri-wallet-3-fill"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Income -->
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div
                                    class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                        <div>
                                            <span class="mb-2 fw-medium text-secondary-light text-md">Total Income</span>
                                            <h6 class="fw-semibold mb-1 income">PKR 00,000</h6>
                                        </div>
                                        <span
                                            class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-200 text-success-600">
                                            <i class="ri-money-dollar-circle-fill"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-body p-24 mb-8">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Income Vs Expense </h6><small>Yearly*</small>
                        </div>
                        <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                            <li class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Income </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <h6 class="mb-0 income-total">0</h6>
                                    <span
                                        class="text-success-600 d-flex align-items-center gap-1 text-sm fw-bolder income-percent">
                                        0%
                                    </span>
                                </div>
                            </li>
                            <li class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold">Expenses </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <h6 class="mb-0 expense-total">0</h6>
                                    <span
                                        class="text-danger-600 d-flex align-items-center gap-1 text-sm fw-bolder expense-percent">
                                        0%
                                    </span>
                                </div>
                            </li>
                        </ul>
                        <div id="incomeExpense" class="apexcharts-tooltip-style-1"></div>

                    </div>
                </div>
            </div>
            <div class="col-xxl-8 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Purchase & Sales</h6>
                            {{-- <select class="form-select form-select-sm w-auto bg-base text-secondary-light">
                                <option>This Month</option>
                                <option>This Week</option>
                                <option>This Year</option>
                            </select> --}}
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-3">
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-8-px rounded-pill bg-warning-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">
                                    Purchase: <span id="purchaseTotal" class="text-primary-light fw-bold">0</span>
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="w-12-px h-8-px rounded-pill bg-success-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">
                                    Sales: <span id="salesTotal" class="text-primary-light fw-bold">0</span>
                                </span>
                            </li>
                        </ul>
                        <div id="purchaseSaleChart" class="margin-16-minus y-value-left"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg">Overall Report</h6>
                            <select class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8">
                                <option>Yearly</option>
                                <option>Monthly</option>
                                <option>Weekly</option>
                                <option>Today</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="mt-32">
                            <div id="userOverviewDonutChart" class="mx-auto apexcharts-tooltip-z-none"></div>
                        </div>
                        <div class="d-flex flex-wrap gap-20 justify-content-center mt-48">
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-warning-600"></span>
                                <span class="text-secondary-light">Purchase</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-lilac-400"></span>
                                {{-- bg-lilac-600 --}}
                                <span class="text-secondary-light">Sales</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-success-600"></span>
                                {{-- bg-warning-600 --}}
                                <span class="text-secondary-light">Expense</span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <span class="w-16-px h-16-px radius-2 bg-lilac-600"></span>
                                <span class="text-secondary-light">Gross Profit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- sale purchase was here --}}
            <div class="col-xxl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Todays POS Summery</h6>
                            <a href="{{ route('view_pos_sale') }}"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0 " id="pos_sale">
                                <thead>
                                    <tr>
                                        <th scope="col">SL</th>
                                        <th scope="col">Date </th>
                                        <th scope="col">Invoice Number</th>
                                        <th scope="col">Grand Total</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                            <h6 class="mb-2 fw-bold text-lg mb-0">Out of Stock Products</h6>
                            {{-- <a href="javascript:void(0)"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-body p-24">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0" id="outOfStockTable">
                                <thead>
                                    <tr>
                                        <th scope="col">SL</th>
                                        <th scope="col">Product Name </th>
                                        <th scope="col">Product Barcode</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    <div id="rightSidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Temporary Bill</h5>
            <button class="btn-close" id="closeSidebar"></button>
        </div>

        <div class="sidebar-body">
            <div id="productWrapper">
                <!-- Product Row -->
                <div class="d-flex align-items-center gap-2 mb-2 product-row">
                    <input type="text" name="product_name[]" class="form-control flex-grow-2"
                        placeholder="Product Name">
                    <input type="number" name="quantity[]" class="form-control quantity w-25" placeholder="Qty"
                        value="1" min="1">
                    <input type="number" name="price[]" class="form-control price w-25" placeholder="Price"
                        value="0" min="0">
                    <input type="number" name="row_total[]" class="form-control row-total w-25" placeholder="Total"
                        readonly>
                    <button type="button" class="btn btn-sm btn-danger removeRow">X</button>
                </div>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-2" id="addRow">+ Add More</button>
        </div>

        <div class="sidebar-footer">
            <h6 class="fw-bold">Grand Total: <span id="grandTotal">0</span></h6>
            <button class="btn btn-primary w-100 mt-2" id="saveInvoice">Save</button>
        </div>
    </div>
    <!-- Your Sidebar (already exists) -->
    <div id="rightSidebar">
        <!-- ... existing code ... -->
        <div class="sidebar-footer">
            <h6 class="fw-bold">Grand Total: <span id="grandTotal">0</span></h6>
            <button class="btn btn-primary w-100 mt-2" id="saveInvoice">Save</button>
        </div>
    </div>
    <style>
        /* Default blur style */
        .stats-blurred {
            filter: blur(6px);
            pointer-events: none;
            user-select: none;
            transition: filter 0.3s ease;
        }

        /* When visible */
        .stats-visible {
            filter: none;
            pointer-events: auto;
        }
    </style>
    <!-- POS Print View (Hidden initially) -->
    @include('admin.layouts.printView')
@endsection
@section('script')
    <script src="{{ asset('admin') }}/assets/js/lib/apexcharts.min.js"></script>
    <script>
        const sale_view = "{{ route('sale_view', ['id' => ':id']) }}";

        $(document).ready(function() {
            var $stats = $('#dailyStats');
            var $toggleBtn = $('#toggleVisibility');
            var $icon = $toggleBtn.find('i');
            var visible = false; // default hidden (blurred)

            $toggleBtn.on('click', function() {
                visible = !visible;
                if (visible) {
                    $stats.removeClass('stats-blurred').addClass('stats-visible');
                    $icon.removeClass('ri-eye-off-line').addClass('ri-eye-line'); // open eye
                } else {
                    $stats.removeClass('stats-visible').addClass('stats-blurred');
                    $icon.removeClass('ri-eye-line').addClass('ri-eye-off-line'); // closed eye
                }
            });
            $.ajax({
                type: "get",
                url: "{{ route('dashboardInfo') }}",
                success: function(response) {
                    // console.log(response);
                    let sale = Math.round(response[0].sale);
                    let purchases = Math.round(response[0].purchases);
                    let expense = Math.round(response[0].expenses);
                    let income = Math.round(response[0].income);
                    $('.sale').text('PKR ' + sale.toLocaleString('en-PK'));
                    $('.purchases').text('PKR ' + purchases.toLocaleString('en-PK'));
                    $('.expense').text('PKR ' + expense.toLocaleString('en-PK'));
                    $('.income').text('PKR ' + income.toLocaleString('en-PK'));
                    // $('.sale').text('PKR ' + Math.round(response[0].sale));

                }
            });

            //pos summery
            $('#pos_sale').DataTable({
                processing: true,
                serverSide: true,
                ajax: 'getPosSale',
                // buttons: ['csv', 'excel', 'pdf'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: null, // indexing ke liye
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sale_date', // purchase_date -> sale_date
                        name: 'sale_summary.sale_date'
                    },
                    {
                        data: 'invoice_number',
                        name: 'sale_summary.invoice_number',
                        // searchable: true,
                        render: function(data, type, row) {
                            // console.log(row.id);
                            if (!data) return '';
                            let url = sale_view.replace(':id', row.id);
                            return `<a href="${url}" class="text-primary" target="_blank">${data}</a>`;
                        }
                    },
                    // {
                    //     data: 'customer_name',   // supplier_name -> customer_name
                    //     name: 'customers.name'
                    // },
                    // {
                    //     data: 'status',
                    //     name: 'sale_summary.status'
                    // },
                    {
                        data: 'grand_total',
                        name: 'sale_summary.grand_total'
                    },
                    // {
                    //     data: 'action',
                    //     orderable: false,
                    //     searchable: false
                    // }
                ],
                error: function(xhr, error, code) {
                    console.log(xhr);
                    console.log(code);
                }

            });
            $("#openSidebar").click(function() {
                $("#rightSidebar").addClass("active");
            });

            $("#closeSidebar").click(function() {
                $("#rightSidebar").removeClass("active");
            });

            // ➕ Add new row
            $("#addRow").click(function() {
                let newRow = `
                                                              <div class="d-flex align-items-center gap-2 mb-2 product-row">
                                                                  <input type="text" name="product_name[]" class="form-control flex-grow-2 mt-2" placeholder="Product Name">
                                                                  <input type="number" name="quantity[]" class="form-control quantity w-25 mt-2" placeholder="Qty" value="1" min="1">
                                                                  <input type="number" name="price[]" class="form-control price w-25 mt-2" placeholder="Price" value="0" min="0">
                                                                  <input type="number" name="row_total[]" class="form-control row-total w-25 mt-2" placeholder="Total" readonly>
                                                                  <button type="button" class="btn btn-sm btn-danger removeRow mt-2">X</button>
                                                              </div>`;
                $("#productWrapper").append(newRow);
            });

            $(document).on("click", ".removeRow", function() {
                $(this).closest(".product-row").remove();
                updateGrandTotal();
            });

            $(document).on("input", ".quantity, .price", function() {
                let row = $(this).closest(".product-row");
                let qty = parseFloat(row.find(".quantity").val()) || 0;
                let price = parseFloat(row.find(".price").val()) || 0;
                let rowTotal = qty * price;
                row.find(".row-total").val(rowTotal.toFixed(2));
                updateGrandTotal();
            });

            function updateGrandTotal() {
                let total = 0;
                $(".row-total").each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $("#grandTotal").text(total.toFixed(2));
            }
            $("#saveInvoice").click(function() {
                let grandTotal = 0;
                let itemsHTML = "";

                $("#productWrapper .product-row").each(function() {
                    let name = $(this).find("input[name='product_name[]']").val();
                    let qty = parseFloat($(this).find(".quantity").val()) || 0;
                    let price = parseFloat($(this).find(".price").val()) || 0;
                    let rowTotal = qty * price;

                    if (name) {
                        itemsHTML += `
                                                            <tr class="product-item">
                                                                <td colspan="3">
                                                                    ${name}<br>
                                                                    <span>${qty} x ${price.toFixed(2)}</span>
                                                                </td>
                                                                <td style="text-align:right; vertical-align:bottom;">${rowTotal.toFixed(2)}</td>
                                                            </tr>`;
                        grandTotal += rowTotal;
                    }
                });

                // 🟢 Remove old product rows
                $("#printModal tbody tr.product-item").remove();

                // 🟢 Add new products before totals
                $("#printModal tbody tr:first").before(itemsHTML);

                // Update totals
                $(".Pgrand_total").text(grandTotal.toFixed(2));
                $(".Pamount_paid").text(grandTotal.toFixed(2));
                $(".Ppaid").text(grandTotal.toFixed(2));
                $(".due").text("0.00");

                $(".Pdiscount").text("0.00");
                $(".Pshipping").text("0.00");
                $(".Pextra_amount").text("0.00");
                $(".Preturn_amount").text("0.00");

                $("#printModal").modal("show");
            });

            // $(document).on("click", ".printNow", function() {
            //     let printContents = document.getElementById("printHere").innerHTML;
            //     let originalContents = document.body.innerHTML;

            //     document.body.innerHTML = printContents;
            //     window.print();
            //     document.body.innerHTML = originalContents;
            //     location.reload();
            // });
            $(document).on("click", ".printNow", function() {
                let printContents = document.querySelector("#printModal .modal-body").innerHTML;

                // ✅ grab full <style> tag (not just inner text)
                let styles = document.getElementById("printStyles").outerHTML;

                let printWindow = window.open("", "", "width=400,height=600");

                printWindow.document.write(`
                                                                <html>
                                                                    <head>
                                                                        <title>Invoice Print</title>
                                                                        ${styles}   <!-- full style tag injected -->
                                                                    </head>
                                                                    <body>
                                                                        ${printContents}
                                                                    </body>
                                                                </html>
                                                            `);

                printWindow.document.close();

                printWindow.onload = function() {
                    printWindow.focus();

                    setTimeout(() => {
                        printWindow.print();

                        printWindow.onafterprint = function() {
                            printWindow.close();
                        };
                    }, 300); // wait to ensure CSS applies
                };
            });

            $(document).on("click", ".btnClose", function() {
                $("#printModal").modal("hide");
                $("#productWrapper").html(`
                                        <div class="d-flex align-items-center gap-2 mb-2 product-row">
                                            <input type="text" name="product_name[]" class="form-control flex-grow-2 mt-2" placeholder="Product Name">
                                            <input type="number" name="quantity[]" class="form-control quantity w-25 mt-2" placeholder="Qty" value="1" min="1">
                                            <input type="number" name="price[]" class="form-control price w-25 mt-2" placeholder="Price" value="0" min="0">
                                            <input type="number" name="row_total[]" class="form-control row-total w-25 mt-2" placeholder="Total" readonly>
                                            <button type="button" class="btn btn-sm btn-danger removeRow mt-2">X</button>
                                        </div>
                                    `);

                // reset grand total
                $("#grandTotal").text("0");
            });


            $('#outOfStockTable').DataTable({
                processing: true,
                searching: false, // search box disable
                ordering: false, // sorting disable
                //paging: false,      // pagination disable
                info: false, // "Showing 1 of X entries" text disable
                ajax: '{{ route('report.outOfStock') }}',
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Serial number
                        }
                    },
                    {
                        data: 'name',
                        title: 'Product Name'
                    },
                    {
                        data: 'barcode',
                        title: 'Product Barcode'
                    }
                ]
            });

        });
        // ===================== Income VS Expense Start =============================== 
        // function createChartTwo(chartId, color1, color2) {
        //     var options = {
        //         series: [{
        //             name: 'income',
        //             data: [48, 35, 50, 32, 48, 40, 55, 50, 60]
        //         }, {
        //             name: 'Expenses',
        //             data: [12, 20, 15, 26, 22, 30, 25, 35, 25]
        //         }],
        //         legend: {
        //             show: false
        //         },
        //         chart: {
        //             type: 'area',
        //             width: '100%',
        //             height: 270,
        //             toolbar: {
        //                 show: false
        //             },
        //             padding: {
        //                 left: 0,
        //                 right: 0,
        //                 top: 0,
        //                 bottom: 0
        //             }
        //         },
        //         dataLabels: {
        //             enabled: false
        //         },
        //         stroke: {
        //             curve: 'smooth',
        //             width: 3,
        //             colors: [color1, color2], // Use two colors for the lines
        //             lineCap: 'round'
        //         },
        //         grid: {
        //             show: true,
        //             borderColor: '#D1D5DB',
        //             strokeDashArray: 1,
        //             position: 'back',
        //             xaxis: {
        //                 lines: {
        //                     show: false
        //                 }
        //             },
        //             yaxis: {
        //                 lines: {
        //                     show: true
        //                 }
        //             },
        //             row: {
        //                 colors: undefined,
        //                 opacity: 0.5
        //             },
        //             column: {
        //                 colors: undefined,
        //                 opacity: 0.5
        //             },
        //             padding: {
        //                 top: -20,
        //                 right: 0,
        //                 bottom: -10,
        //                 left: 0
        //             },
        //         },
        //         colors: [color1, color2], // Set color for series
        //         fill: {
        //             type: 'gradient',
        //             colors: [color1, color2], // Use two colors for the gradient
        //             // gradient: {
        //             //     shade: 'light',
        //             //     type: 'vertical',
        //             //     shadeIntensity: 0.5,
        //             //     gradientToColors: [`${color1}`, `${color2}00`], // Bottom gradient colors with transparency
        //             //     inverseColors: false,
        //             //     opacityFrom: .6,
        //             //     opacityTo: 0.3,
        //             //     stops: [0, 100],
        //             // },
        //             gradient: {
        //                 shade: 'light',
        //                 type: 'vertical',
        //                 shadeIntensity: 0.5,
        //                 gradientToColors: [undefined, `${color2}00`], // Apply transparency to both colors
        //                 inverseColors: false,
        //                 opacityFrom: [0.4, 0.6], // Starting opacity for both colors
        //                 opacityTo: [0.3, 0.3], // Ending opacity for both colors
        //                 stops: [0, 100],
        //             },
        //         },
        //         markers: {
        //             colors: [color1, color2], // Use two colors for the markers
        //             strokeWidth: 3,
        //             size: 0,
        //             hover: {
        //                 size: 10
        //             }
        //         },
        //         xaxis: {
        //             labels: {
        //                 show: false
        //             },
        //             categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        //             tooltip: {
        //                 enabled: false
        //             },
        //             labels: {
        //                 formatter: function(value) {
        //                     return value;
        //                 },
        //                 style: {
        //                     fontSize: "14px"
        //                 }
        //             }
        //         },
        //         yaxis: {
        //             labels: {
        //                 formatter: function(value) {
        //                     return "$" + value + "k";
        //                 },
        //                 style: {
        //                     fontSize: "14px"
        //                 }
        //             },
        //         },
        //         tooltip: {
        //             x: {
        //                 format: 'dd/MM/yy HH:mm'
        //             }
        //         }
        //     };

        //     var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
        //     chart.render();
        // }

        function createChartTwo(chartId, color1, color2, year = null) {
            $.ajax({
                url: '{{ route('stock.report.chartData') }}',
                type: 'GET',
                data: {
                    year: year
                },
                success: function(response) {
                    // Chart render
                    // var options = {
                    //     series: response.series,
                    //     chart: { type: 'area', width: '100%', height: 270, toolbar: { show: false } },
                    //     stroke: { curve: 'smooth', width: 3, colors: [color1, color2] },
                    //     xaxis: { categories: response.labels },
                    //     colors: [color1, color2],
                    // };
                    // var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
                    // chart.render();

                    var options = {
                        series: response.series, // Laravel se series aa rahi hai
                        legend: {
                            show: false
                        },
                        chart: {
                            type: 'area',
                            width: '100%',
                            height: 270,
                            toolbar: {
                                show: false
                            },
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                            colors: [color1, color2],
                            lineCap: 'round'
                        },
                        grid: {
                            show: true,
                            borderColor: '#D1D5DB',
                            strokeDashArray: 1,
                            position: 'back',
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            row: {
                                opacity: 0.5
                            },
                            column: {
                                opacity: 0.5
                            },
                            padding: {
                                top: -20,
                                right: 0,
                                bottom: -10,
                                left: 0
                            },
                        },
                        colors: [color1, color2],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                type: 'vertical',
                                shadeIntensity: 0.5,
                                gradientToColors: [undefined, `${color2}00`],
                                inverseColors: false,
                                opacityFrom: [0.4, 0.6],
                                opacityTo: [0.3, 0.3],
                                stops: [0, 100],
                            },
                        },
                        markers: {
                            colors: [color1, color2],
                            strokeWidth: 3,
                            size: 0,
                            hover: {
                                size: 10
                            }
                        },
                        xaxis: {
                            categories: response.labels, // Laravel se months labels
                            tooltip: {
                                enabled: false
                            },
                            labels: {
                                formatter: function(value) {
                                    return value;
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return "$" + value.toFixed(0); // remove k if you want exact
                                },
                                style: {
                                    fontSize: "14px"
                                }
                            }
                        },
                        tooltip: {
                            x: {
                                format: 'MMM'
                            }
                        }
                    };

                    var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
                    chart.render();

                    // === Update summary HTML dynamically ===
                    $('#incomeExpense')
                        .closest('.card-body')
                        .find('.income-total').text(`${response.summary.totalIncome.toLocaleString()}`);
                    $('#incomeExpense')
                        .closest('.card-body')
                        .find('.expense-total').text(`${response.summary.totalExpenses.toLocaleString()}`);

                    // Income percent
                    let incomeHtml =
                        `${response.summary.percentIncome}% <i class="ri-arrow-${response.summary.percentIncome >= 0 ? 'up' : 'down'}-s-fill d-flex"></i>`;
                    $('#incomeExpense')
                        .closest('.card-body')
                        .find('.income-percent')
                        .html(incomeHtml);

                    // Expense percent
                    let expenseHtml =
                        `${response.summary.percentExpense}% <i class="ri-arrow-${response.summary.percentExpense >= 0 ? 'up' : 'down'}-s-fill d-flex"></i>`;
                    $('#incomeExpense')
                        .closest('.card-body')
                        .find('.expense-percent')
                        .html(expenseHtml);
                }
            });
        }


        createChartTwo('incomeExpense', '#487FFF', '#FF9F29');
        // ===================== Income VS Expense End =============================== 

        // ================================ Users Overview Donut chart Start ================================ 
        // var options = {
        //     series: [30, 30, 20, 20],
        //     colors: ['#FF9F29', '#487FFF', '#45B369', '#9935FE'],
        //     labels: ['Purchase', 'Sales', 'Expense', 'Gross Profit'],
        //     legend: {
        //         show: false
        //     },
        //     chart: {
        //         type: 'donut',
        //         height: 270,
        //         sparkline: {
        //             enabled: true // Remove whitespace
        //         },
        //         margin: {
        //             top: 0,
        //             right: 0,
        //             bottom: 0,
        //             left: 0
        //         },
        //         padding: {
        //             top: 0,
        //             right: 0,
        //             bottom: 0,
        //             left: 0
        //         }
        //     },
        //     stroke: {
        //         width: 0,
        //     },
        //     dataLabels: {
        //         enabled: true
        //     },
        //     responsive: [{
        //         breakpoint: 480,
        //         options: {
        //             chart: {
        //                 width: 200
        //             },
        //             legend: {
        //                 position: 'bottom'
        //             }
        //         }
        //     }],
        // };

        // var chart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), options);
        // chart.render();
        var overallChart; // global variable

        function createOverallDonutChart(filter = 'yearly') {
            $.ajax({
                url: "{{ route('stock.report.overall') }}",
                type: "GET",
                data: {
                    filter: filter
                },
                success: function(response) {
                    // console.log(response);
                    var options = {
                        series: response.series,
                        colors: ['#FF9F29', '#487FFF', '#45B369', '#9935FE'],
                        labels: response.labels,
                        legend: {
                            show: false
                        },
                        chart: {
                            type: 'donut',
                            height: 270,
                            sparkline: {
                                enabled: true
                            }
                        },
                        stroke: {
                            width: 0
                        },
                        dataLabels: {
                            enabled: true
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }],
                    };

                    if (overallChart) {
                        overallChart.updateOptions(options);
                    } else {
                        overallChart = new ApexCharts(document.querySelector("#userOverviewDonutChart"),
                            options);
                        overallChart.render();
                    }
                }
            });
        }

        // Page load pe yearly call
        createOverallDonutChart();

        // Dropdown change
        $(document).on("change", ".form-select", function() {
            var filter = $(this).val().toLowerCase(); // Yearly -> yearly
            createOverallDonutChart(filter);
        });
        // ================================ Users Overview Donut chart End ================================ 

        // ================================ Purchase & sale chart End ================================ 
        // var options = {
        //     series: [{
        //         name: 'Net Profit',
        //         data: [44, 100, 40, 56, 30, 58, 50]
        //     }, {
        //         name: 'Free Cash',
        //         data: [60, 120, 60, 90, 50, 95, 90]
        //     }],
        //     colors: ['#45B369', '#FF9F29'],
        //     labels: ['Active', 'New', 'Total'],

        //     legend: {
        //         show: false
        //     },
        //     chart: {
        //         type: 'bar',
        //         height: 260,
        //         toolbar: {
        //             show: false
        //         },
        //     },
        //     grid: {
        //         show: true,
        //         borderColor: '#D1D5DB',
        //         strokeDashArray: 4, // Use a number for dashed style
        //         position: 'back',
        //     },
        //     plotOptions: {
        //         bar: {
        //             borderRadius: 4,
        //             columnWidth: 8,
        //         },
        //     },
        //     dataLabels: {
        //         enabled: false
        //     },
        //     states: {
        //         hover: {
        //             filter: {
        //                 type: 'none'
        //             }
        //         }
        //     },
        //     stroke: {
        //         show: true,
        //         width: 0,
        //         colors: ['transparent']
        //     },
        //     xaxis: {
        //         categories: ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'],
        //     },
        //     fill: {
        //         opacity: 1,
        //         width: 18,
        //     },
        // };

        // var chart = new ApexCharts(document.querySelector("#purchaseSaleChart"), options);
        // chart.render();
        function createPurchaseSaleChart(chartId) {
            $.ajax({
                url: '{{ route('stock.report.chartDataPurchaseSaleWeek') }}',
                type: 'GET',
                success: function(response) {
                    // Chart options
                    var options = {
                        series: response.series,
                        colors: ['#FF9F29', '#45B369'], // purchase = orange, sales = green
                        chart: {
                            type: 'bar',
                            height: 260,
                            toolbar: {
                                show: false
                            },
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: 8,
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: response.labels
                        },
                        grid: {
                            show: true,
                            borderColor: '#D1D5DB',
                            strokeDashArray: 4,
                        },
                    };

                    var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
                    chart.render();

                    // 🔹 Update summary HTML dynamically
                    $('#purchaseTotal').text(`${response.totals.purchase}`);
                    $('#salesTotal').text(`${response.totals.sales}`);
                }
            });
        }

        // call function
        createPurchaseSaleChart('purchaseSaleChart');


        // ================================ Purchase & sale chart End ================================ 
    </script>
@endsection
