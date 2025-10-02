@extends('admin.layouts.master')

@section('page-title')
    Purchase Ledger Report
@endsection

@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Purchase Ledger Report</h6>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="supplier_id" class="form-control supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
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
                    <table id="purchaseLedgerTable" class="table table-bordered">
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
        const purchase_view = "{{ route('purchase_view', ['id' => ':id']) }}";

        $(document).ready(function () {
            $('.supplier_id').select2();
            loadData();

            function loadData(from_date = '', to_date = '', supplier_id = '') {
                $('#purchaseLedgerTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    paging: false,
                    ajax: function (data, callback, settings) {
                        var str_url = "{{ route('reports.purchase.ledger.data') }}";
                        var str_data = {
                            from_date: from_date,
                            to_date: to_date,
                            supplier_id: supplier_id,
                            ...data
                        };

                        CustomAjax(str_url, "GET", str_data, "json", function (response) {
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
                                if (!row.purchase_id) {
                                    return `<span class="text-success fw-400">CASH</span>`;
                                }
                                let url = purchase_view.replace(':id', row.purchase_id);
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
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };
                        var totalDebit = api.column(4, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                        var totalCredit = api.column(5, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);
                        var totalBalance = api.column(6, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0);

                        $(api.column(4).footer()).html(totalDebit.toFixed(2));
                        $(api.column(5).footer()).html(totalCredit.toFixed(2));
                        $(api.column(6).footer()).html(totalBalance.toFixed(2));
                    }
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