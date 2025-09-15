@extends('admin.layouts.master')

@section('page-title')
    Purchase Report
@endsection

@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Purchase Report</h6>
        </div>

        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')

            <div class="card">
                <div class="card-header">
                    <div class="row mb-3" id="filters">
                        <div class="col-md-3">
                            <input type="date" id="from_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="to_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <select id="supplier_id" class="form-control">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="filter" class="btn btn-primary">Filter</button>
                            <button id="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="purchaseReportTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Product Name</th>
                                <th>Qty Purchased</th>
                                <th>Grand Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            loadData();

            function loadData(from_date = '', to_date = '', supplier_id = '') {
                $('#purchaseReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: function (data, callback, settings) {
                        var str_url = "{{ route('reports.purchase_report.data') }}";
                        var str_method = "GET";
                        var str_data_type = "json";
                        var str_data = {
                            from_date: from_date,
                            to_date: to_date,
                            supplier_id: supplier_id,
                            ...data // 🔹 DataTables ke paging/search params bhi bhejenge
                        };

                        CustomAjax(str_url, str_method, str_data, str_data_type, function (response) {
                            callback(response); // 🔹 DataTables ko response return
                        });
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'date', name: 'purchases.purchase_date' },
                        { data: 'reference', name: 'purchases.reference_no' },
                        { data: 'supplier', name: 'suppliers.name' },
                        { data: 'product_name', name: 'products.name' },
                        { data: 'qty_purchased', searchable: false },
                        { data: 'grand_total', searchable: false }
                    ]
                });
            }

            $('#filter').click(function () {
                loadData($('#from_date').val(), $('#to_date').val(), $('#supplier_id').val());
            });

            $('#reset').click(function () {
                $('#from_date').val('');
                $('#to_date').val('');
                $('#supplier_id').val('');
                loadData();
            });
        });
    </script>
@endsection