@extends('admin.layouts.master')

@section('page-title')
    Stock Report
@endsection

@section('main-content')
    <div class="dashboard-main-body">
       <div class="card">
        <div class="card-body">
            <h4>Stock Report</h4>
            <table class="table table-bordered" id="stockReportTable">
                <thead>
                    <tr>
                        <th class="text-start">Serial</th>
                        <th class="text-start">Product Code</th>
                        <th>Product Name</th>
                        <th class="text-start">Total Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
       </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#stockReportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock.report.data') }}",
                columns: [
                    { data: 'serial' },
                    { data: 'code' },
                    { data: 'name' },
                    { data: 'stock' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
