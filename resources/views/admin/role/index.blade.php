@extends('admin.layouts.master')

@section('page-title')
    Roles List
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Roles List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Roles List
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            @if (isset(Auth::user()->hasPer('Role')['pcreate']) && Auth::user()->hasPer('Role')['pcreate'] == 1)
                                <a href="{{ route('role.create') }}" class="btn btn-success">Add New Role</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th scope="col" width="50px">S.L</th>
                                        <th scope="col">Role Name</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td></td> 
                                            <td>{{ $role->name }}</td>
                                            <td class="d-flex">
                                                <a href="{{ route('role.edit', $role->id) }}"
                                                    class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                                </a>
                                                <form method="post" action="{{ route('role_delete') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" value="{{ $role->id }}" name="id">
                                                    <button type="submit"
                                                        class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                columnDefs: [{
                    targets: 0, // First column
                    orderable: false,
                    searchable: false,
                    className: 'dt-body-left'
                }],
                order: [
                    [1, 'asc']
                ], // Order by name column by default
                createdRow: function(row, data, dataIndex) {
                    // Add data-index attribute for reference
                    $(row).attr('data-index', dataIndex);
                },
                drawCallback: function(settings) {
                    // Update serial numbers on each draw
                    var api = this.api();
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = api.page() * api.page.len() + i + 1;
                    });
                }
            });
        });
    </script>
@endsection
