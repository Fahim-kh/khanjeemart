@extends('admin.layouts.master')

@section('page-title')
    Module
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Module</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Module
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    @if (isset(Auth::user()->hasPer('Module')['pcreate']) && Auth::user()->hasPer('Module')['pcreate'] == 1)
                    <a href="#" class="btn btn-success btn-addStudent"
                    data-bs-toggle="modal" data-bs-target="#createModal">
                    Add New Module
                 </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter  border text-nowrap moduleTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Module Name</th>
                                    <th>Module Route</th>
                                    <th>Parient Module</th>
                                    <th>Icon</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modules as $key => $module)
                                    <tr data-id="{{ $module->id }}">
                                       <td>{{ $key+1 }}</td>
                                       <td>{{ $module->name }}</td>
                                       <td>{{ $module->route }}</td>
                                       <td>{{ $module->parent_id }}</td>
                                       <td>{{ $module->icon }}</td>
                                        <td>
                                            @if(isset(Auth::user()->hasPer('Module')['pedit']) && Auth::user()->hasPer('Module')['pedit'] == 1)
                                                <a href="{{ route('module.edit',['id'=>$module->id]) }}"
                                                class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            @endif
                                            @if(isset(Auth::user()->hasPer('Module')['pdelete']) && Auth::user()->hasPer('Module')['pdelete'] == 1)
                                                <a href="#" class="moduleDelete w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" role="button"  title="Delete" data-id="{{ $module->id }}"> <iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                        <tr><td>Opps! no module added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="createModal" class="modal fade" data-backdrop="static" data-keyboard="false" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="overflow-y: auto; padding:10px;">
                <div class="modal-header pt-0">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                <div class="container popup-tabs model_body">
                    <form action="#">
                        <div class="form-group">
                            <label for="module_name">Module Name</label>
                            <input type="text" class="form-control" id="module_name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="module_route">Module Route (put # for empty route)</label>
                            <input type="text" class="form-control" id="module_route" name="route">
                        </div>
                        <div class="form-group">
                            <label for="parent_module">Parent Module</label>
                            <select name="parent_id" id="parent_module" class="form-control">
                                <option selected disabled>Please Select Parent Module</option>
                                @foreach($modules as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="module_icon">Module Icon</label>
                            <input type="text" class="form-control" id="module_icon" name="icon">
                        </div>
                        <div class="form-group">
                            <label for="icon_type">Icon Type</label>
                            <select name="icon_type" id="icon_type" class="form-control">
                                <option value="html">Html</option>
                                <option value="class">Class</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="module_sort">Module Sort</label>
                            <input type="number" class="form-control" id="module_sort"min="1" name="sort">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" id="btn-submit">Save</button>
                            <button type="button" class="btn btn-danger" id="btn-submit" data-bs-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- modal-dialog -->
    </div>
    
    <div id="deleteModal" class="modal fade" data-backdrop="static" data-keyboard="false" >
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="overflow-y: auto; padding:10px;">
                <div class="container popup-tabs model_body">
                    <center>
                        <p>Are you sure you want to delete this?<br></p>
                        <button type="submit" id="confirmDeleteLocation" class="btn btn-secondary locationDelete">Yes</button>
                        <button class="btn btn-danger" data-dismiss="modal" aria-label="Close">No</button>
                    </center>
                </div>
            </div>
        </div><!-- modal-dialog -->
    </div>
    <style>
        .form-group{
            margin-top:25px !important;
        }
    </style>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
    $('.moduleTable').dataTable();
    $('#btn-submit').click(function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way
        var token = '{{ csrf_token() }}';
        var name = $('#module_name').val();
        var route = $('#module_route').val();
        var parent_id = $('#parent_module').val();
        var icon_type = $('#icon_type').val();
        var icon = $('#module_icon').val();
        var sort = $('#module_sort').val()

        $.ajax({
            url: '{{ route("module.store") }}',
            type: 'POST',
            data: {
                _token:token,
                name:name,
                route:route,
                parent_id:parent_id,
                icon_type:icon_type,
                icon:icon,
                sort:sort,
            },
            success: function (response) {
               if(response.success){
                    $('#createModal').modal('hide');
                    window.location.reload();
               }
            },
            error: function(response) {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#module_'+key).addClass('is-invalid');
                    });
                }
            }
        });
    });
    $('.moduleDelete').on('click', function () {
        var moduleId = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "This category will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('module.destroy', ['id' => '__ID__']) }}".replace('__ID__', moduleId),
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            button.closest('tr').remove();
                        } else {
                            Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });
});
    </script>
@endsection
