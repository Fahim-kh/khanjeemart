@extends('admin.layouts.master')

@section('page-title')
    Product Report
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Product Report</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('purchase.index') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Product Report
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')


            <div class="row mb-3" id="summaryFilters">
                <div class="col-md-3">
                    <input type="date" id="from_date_summary" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="date" id="to_date_summary" class="form-control">
                </div>
                <div class="col-md-3">
                    <button id="filterSummary" class="btn btn-primary">Filter</button>
                    <button id="resetSummary" class="btn btn-secondary">Reset</button>
                </div>
            </div>

            <table id="productReportTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Total Sales</th>
                        <th>Total Sales Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>



            <div id="productDetailsSection" style="display:none; margin-top:20px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>Product Sale Details</h5>
                    <button id="backToSummary" class="btn btn-secondary btn-sm">Back</button>
                </div>

                <div class="row mb-3" id="detailFilters">
                    <div class="col-md-3">
                        <input type="date" id="from_date_detail" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="to_date_detail" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button id="filterDetail" class="btn btn-primary">Filter</button>
                        <button id="resetDetail" class="btn btn-secondary">Reset</button>
                    </div>
                </div>

                <table class="table table-bordered" id="productDetailsTable">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>


        </div>
    </div>
@endsection

@section('script')

    <script>
        const baseUrl = "{{ env('APP_URL') }}";
    </script>

    <script>
        $(document).ready(function () {
            // ---------- Summary Table ----------
            loadSummary();

            function loadSummary(from_date = '', to_date = '') {
                $('#productReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('getData') }}",
                        data: { from_date: from_date, to_date: to_date }
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'product_code', name: 'products.sku' },
                        { data: 'product_name', name: 'products.name' },
                        { data: 'total_sales', searchable: false },
                        { data: 'total_sales_amount', searchable: false },
                        { data: 'action', orderable: false, searchable: false }
                    ]
                });
            }

            $('#filterSummary').click(function () {
                let from = $('#from_date_summary').val();
                let to = $('#to_date_summary').val();

                // 🔄 detail filters ko sync karna
                $('#from_date_detail').val(from);
                $('#to_date_detail').val(to);

                loadSummary(from, to);
            });

            $('#resetSummary').click(function () {
                $('#from_date_summary').val('');
                $('#to_date_summary').val('');

                // 🔄 detail filters ko bhi reset karna
                $('#from_date_detail').val('');
                $('#to_date_detail').val('');

                loadSummary();
            });

            // ---------- Product Details ----------
            function loadDetails(product_id, from_date = '', to_date = '') {
                var url = "{{ route('reports.product.details', ':id') }}"
                    .replace(':id', product_id);

                $.get(url, { from_date: from_date, to_date: to_date }, function (data) {
                    var rows = '';
                    data.forEach(function (item) {
                        rows += `
                            <tr>
                                <td>${item.invoice_number}</td>
                                <td>${item.sale_date}</td>
                                <td>${item.customer_name}</td>
                                <td>${item.quantity}</td>
                                <td>${item.selling_unit_price}</td>
                                <td>${item.subtotal}</td>
                            </tr>`;
                    });

                    $('#productDetailsTable tbody').html(rows);
                });
            }

            $(document).on('click', '.view-details', function (e) {
                e.preventDefault();

                var product_id = $(this).data('id');

                $('#productReportTable_wrapper').hide();
                $('#summaryFilters').hide();

                $('#productDetailsSection').show();

                //loadDetails(product_id);
                loadDetails(product_id, $('#from_date_detail').val(), $('#to_date_detail').val());

                // bind filter for details
                $('#filterDetail').off('click').on('click', function () {
                    loadDetails(product_id, $('#from_date_detail').val(), $('#to_date_detail').val());
                });

                $('#resetDetail').off('click').on('click', function () {
                    $('#from_date_detail').val('');
                    $('#to_date_detail').val('');
                    loadDetails(product_id);
                });
            });

            $('#backToSummary').click(function () {
                $('#productDetailsSection').hide();
                $('#productReportTable_wrapper').show();
                $('#summaryFilters').show();
            });
        });

    </script>

@endsection