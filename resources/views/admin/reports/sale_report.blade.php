@extends('admin.layouts.master')

@section('page-title')
    Product Sale Report
@endsection

@section('main-content')
<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Product Sale Report</h6>
    </div>

    <div class="container">
        @include('admin.layouts.errorLayout')
        @include('admin.layouts.successLayout')

        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="from_date" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" id="to_date" class="form-control">
            </div>
            <div class="col-md-3">
                <select id="customer_id" class="form-control">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button id="filter" class="btn btn-primary">Filter</button>
                <button id="reset" class="btn btn-secondary">Reset</button>
            </div>
        </div>

        <table id="saleReportTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty Sold</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        loadData();

        function loadData(from_date = '', to_date = '', customer_id = '') {
            $('#saleReportTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: function (data, callback, settings) {
                    var str_url = "{{ route('reports.sale_report.data') }}";
                    var str_method = "GET";
                    var str_data_type = "json";
                    var str_data = {
                        from_date: from_date,
                        to_date: to_date,
                        customer_id: customer_id,
                        ...data
                    };

                    CustomAjax(str_url, str_method, str_data, str_data_type, function (response) {
                        callback(response);
                    });
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'date', name: 'sale_summary.sale_date' },
                    { data: 'reference', name: 'sale_summary.invoice_number' },
                    { data: 'customer', name: 'customers.name' },
                    { data: 'product_name', name: 'products.name' },
                    { data: 'qty_sold', searchable: false },
                    { data: 'grand_total', searchable: false },
                ]
            });
        }

        $('#filter').click(function () {
            loadData($('#from_date').val(), $('#to_date').val(), $('#customer_id').val());
        });

        $('#reset').click(function () {
            $('#from_date').val('');
            $('#to_date').val('');
            $('#customer_id').val('');
            loadData();
        });
    });
</script>
@endsection
