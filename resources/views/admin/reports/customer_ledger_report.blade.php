@extends('admin.layouts.master')

@section('page-title')
    Customer Ledger Report
@endsection

@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Customer Ledger Report</h6>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="customer_id" class="form-control customer_id">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                    <table id="customerLedgerTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-start">Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th class="text-start">Debit</th>
                                <th class="text-start">Credit</th>
                                <th class="text-start">Balance</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-start"></th>
                                <th class="text-start"></th>
                                <th class="text-start"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2-container, .select2-selection, .select2-dropdown {
        width: 250.5px !important;
    }
    </style>
@endsection

@section('script')
<script>
const sale_view = "{{ route('sale_view', ['id' => ':id']) }}";

$(document).ready(function () {
    loadData();
    $('.customer_id').select2();
    function loadData(from_date = '', to_date = '', customer_id = '') {
        $('#customerLedgerTable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            paging: false,
            ajax: function (data, callback, settings) {
                var str_url = "{{ route('reports.customer.ledger.data') }}";
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
                { data: 'date', name: 'date' },
                { 
                    data: 'reference', 
                    name: 'reference',
                    render: function (data, type, row) {
                        if (!data || data === '---') return '';
                        if (!row.sale_id) {
                            return `<span class="text-success fw-400">CASH</span>`;
                        }
                        let url = sale_view.replace(':id', row.sale_id);
                        return `<a href="${url}" class="text-primary" target="_blank">${data}</a>`;
                    }
                },
                { data: 'description', name: 'description' },
                { data: 'debit', name: 'debit' },
                { data: 'credit', name: 'credit' },
                { data: 'balance', name: 'balance' },
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // helper fn to parse numbers
                var intVal = function (i) {
                    return typeof i === 'string'
                        ? i.replace(/[\$,]/g, '')*1
                        : typeof i === 'number'
                            ? i : 0;
                };

                // total debit
                var totalDebit = api
                    .column(4, { page: 'current'} )
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // total credit
                var totalCredit = api
                    .column(5, { page: 'current'} )
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // total balance
                var totalBalance = api
                    .column(6, { page: 'current'} )
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                    var dubBalance = totalDebit - totalCredit;
                // Update footer
                $(api.column(4).footer()).html(totalDebit.toFixed(2));
                $(api.column(5).footer()).html(totalCredit.toFixed(2));
                $(api.column(6).footer()).html(dubBalance.toFixed(2));
            }
        });
    }

    $('#filter').click(function () {
        let from = $('#from_date').val();
        let to = $('#to_date').val();
        let customer_id = $('#customer_id').val();
        loadData(from, to, customer_id);
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
