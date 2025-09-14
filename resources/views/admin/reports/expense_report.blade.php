@extends('admin.layouts.master')

@section('page-title')
    Expense Report
@endsection

@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Expense Report</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Expense Report
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
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="date" id="from_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="to_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button id="filter" class="btn btn-primary">Filter</button>
                            <button id="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="expenseReportTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-start">Date</th>
                                <th class="text-start">Amount</th>
                                <th>Category</th>
                                <th>Created By</th>
                                <th>Description</th>
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

            function loadData(from_date = '', to_date = '') {
                $('#expenseReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: function (data, callback, settings) {
                        var str_url = "{{ route('reports.expense.data') }}";
                        var str_method = "GET";
                        var str_data_type = "json";
                        var str_data = {
                            from_date: from_date,
                            to_date: to_date,
                            ...data // 🔹 DataTables ke apne paging/search params bhi bhejne k liye
                        };

                        CustomAjax(str_url, str_method, str_data, str_data_type, function (response) {
                            callback(response); // 🔹 DataTables ko response wapas do
                        });
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'date', name: 'expense.date' },
                        { data: 'amount', name: 'expense.amount' },
                        { data: 'category_name', name: 'expense_categories.name' },
                        { data: 'created_by_name', name: 'users.name' },
                        { data: 'description', name: 'expense.description' },
                    ]
                });

            }

            $('#filter').click(function () {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                loadData(from_date, to_date);
            });

            $('#reset').click(function () {
                $('#from_date').val('');
                $('#to_date').val('');
                loadData();
            });
        });
    </script>
@endsection