@extends('admin.layouts.master')

@section('page-title')
    Product Report
@endsection
@section('main-content')
<style>
    .table>:not(caption)>*>* {
    font-size: 14px;
    font-weight: 400;
    padding: 0.4rem .4rem;
    color: var(--bs-table-color-state, var(--bs-table-color-type, var(--bs-table-color)));
    background-color: var(--bs-table-bg);
    border-bottom-width: var(--bs-border-width);
    box-shadow: inset 0 0 0 9999px var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-accent-bg)));
}
#downloadDetailPdf:hover i{
    color:#fff;
}
#downloadSummaryPdf:hover i{
    color:#fff;
}
</style>
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

            <div class="card">
                <div class="card-header">
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
                        <div class="col-md-3">
                            <a href="#" id="downloadSummaryPdf" class="btn btn-outline-danger btn-sm" target="_blank"><span class="text-danger"><i class="ri-file-text-fill"></i></span> Download Summary PDF</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="productReportTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Barcode</th>
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

                            <div class="col-md-3">
                                <a href="#" id="downloadDetailPdf" class="btn btn-outline-danger btn-sm" target="_blank"><span class="text-danger"><i class="ri-file-text-fill"></i></span> Download Summary PDF</a>
                            </div>
                        </div>

                        <table class="table table-bordered" id="productDetailsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th>Created By</th>
                                    <th>Customer</th>
                                    <th>Product Name</th>
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
        </div>
    </div>
@endsection

@section('script')
    <script>
        const baseUrl = "{{ env('APP_URL') }}";
    </script>

    <script>
        $(document).ready(function() {
            // ---------- Summary Table ----------
            loadSummary();
            updateSummaryPdfLink();

            function loadSummary(from_date = '', to_date = '') {
                $('#productReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "{{ route('getData') }}",
                        data: {
                            from_date: from_date,
                            to_date: to_date
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_barcode',
                            name: 'products.barcode',
                            render: function(data, type, row) {
                                return '<span class="badge bg-secondary">' + data + '</span>';
                            }
                        },
                        {
                            data: 'product_name',
                            name: 'products.name',
                            render: function(data, type, row) {
                                return '<span class="badge bg-success">' + data + '</span>';
                            }
                        },
                        {
                            data: 'total_sales',
                            searchable: false,
                            render: function(data, type, row) {
                                return data + ' ' + row.unit_name;
                            }
                        },
                        {
                            data: 'total_sales_amount',
                            searchable: false
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            $('#filterSummary').click(function() {
                let from = $('#from_date_summary').val();
                let to = $('#to_date_summary').val();

                // 🔄 detail filters ko sync karna
                $('#from_date_detail').val(from);
                $('#to_date_detail').val(to);

                loadSummary(from, to);
                updateSummaryPdfLink();
            });

            $('#resetSummary').click(function() {
                $('#from_date_summary').val('');
                $('#to_date_summary').val('');

                // 🔄 detail filters ko bhi reset karna
                $('#from_date_detail').val('');
                $('#to_date_detail').val('');

                loadSummary();
                updateSummaryPdfLink();
            });

            // ---------- Product Details ----------
            function loadDetails(product_id, from_date = '', to_date = '') {
                var url = "{{ route('reports.product.details', ':id') }}"
                    .replace(':id', product_id);

                $.get(url, {
                    from_date: from_date,
                    to_date: to_date
                }, function(data) {
                    var rows = '';
                    data.forEach(function(item) {
                        rows += `
                            <tr>
                                <td>${item.sale_date}</td>
                                <td>${item.invoice_number}</td>
                                <td>${item.created_by_name}</td>
                                <td>${item.customer_name}</td>
                                <td>${item.product_name}</td>
                                <td>${item.quantity+' '+item.unit_name}</td>
                                <td>${item.selling_unit_price}</td>
                                <td>${item.subtotal}</td>
                            </tr>`;
                    });

                    $('#productDetailsTable tbody').html(rows);
                });
            }

            $(document).on('click', '.view-details', function(e) {
                e.preventDefault();

                var product_id = $(this).data('id');

                $('#productReportTable_wrapper').hide();
                $('#summaryFilters').hide();

                $('#productDetailsSection').show();

                //loadDetails(product_id);
                loadDetails(product_id, $('#from_date_detail').val(), $('#to_date_detail').val());
                updateDetailPdfLink(product_id);
                // bind filter for details
                $('#filterDetail').off('click').on('click', function() {
                    loadDetails(product_id, $('#from_date_detail').val(), $('#to_date_detail')
                    .val());
                    updateDetailPdfLink(product_id);

                });

                $('#resetDetail').off('click').on('click', function() {
                    $('#from_date_detail').val('');
                    $('#to_date_detail').val('');
                    loadDetails(product_id);
                    updateDetailPdfLink(product_id);
                });
               
            });

            $('#backToSummary').click(function() {
                $('#productDetailsSection').hide();
                $('#productReportTable_wrapper').show();
                $('#summaryFilters').show();
            });
           
        });
        function updateSummaryPdfLink() {
            let from = $('#from_date_summary').val();
            let to = $('#to_date_summary').val();

            let url = "{{ route('reports.products.pdf') }}" + "?from_date=" + from + "&to_date=" + to;
            $('#downloadSummaryPdf').attr('href', url);
        }
        function updateDetailPdfLink(product_id) {
            let from = $('#from_date_detail').val();
            let to = $('#to_date_detail').val();

            let url = baseUrl + '/admin/reports/product_detail/' + product_id + '/pdf?from_date=' + from + '&to_date=' + to;
            $('#downloadDetailPdf').attr('href', url);
            console.log(url);
        }

        
    </script>
@endsection
