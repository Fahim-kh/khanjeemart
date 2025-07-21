@extends('admin.layouts.master')

@section('page-title')
    Roles Create
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Roles Create</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Roles Create
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
                        <div class="card-body">
                            <form action="{{ route('role.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label">Name</label>
                                            <input type="text"
                                                class="form-control  @error('name') is-invalid state-invalid @enderror"
                                                name="name" placeholder="Enter Role Name">
                                            @error('name')
                                                <div class="invalid-feedback" role="alert">{{ $errors->first('name') }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive ">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="wd-15p border-bottom-0">Name</th>
                                                <th class="wd-15p border-bottom-0">View</th>
                                                <th class="wd-15p border-bottom-0">Create</th>
                                                <th class="wd-15p border-bottom-0">Edit</th>
                                                <th class="wd-15p border-bottom-0">Delete</th>
                                            </tr>
                                        </thead>
                                        @foreach ($modules as $module)
                                            <input type="hidden" name="moduleid[{{ $module->id }}]"
                                                value="{{ $module->id }}">
                                            <tbody class="rolemodules">
                                                <tr>
                                                    <td>{{ $module->name }}</td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input type="checkbox" name="view[{{ $module->id }}]"
                                                                id="view_{{ $module->id }}" class="form-check-input"
                                                                @if (old("view.{$module->id}")) checked @endif>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input type="checkbox" name="create[{{ $module->id }}]"
                                                                id="create_{{ $module->id }}" class="form-check-input"
                                                                @if (old("create.{$module->id}")) checked @endif>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input type="checkbox" name="edit[{{ $module->id }}]"
                                                                id="edit_{{ $module->id }}" class="form-check-input"
                                                                @if (old("edit.{$module->id}")) checked @endif>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input type="checkbox" name="delete[{{ $module->id }}]"
                                                                id="delete_{{ $module->id }}" class="form-check-input"
                                                                @if (old("delete.{$module->id}")) checked @endif>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <style>
                                                    .simple-checkbox {
                                                        width: 18px;
                                                        height: 18px;
                                                        cursor: pointer;
                                                    }

                                                    .simple-checkbox+label {
                                                        display: inline-block;
                                                        vertical-align: middle;
                                                        cursor: pointer;
                                                    }
                                                </style>
                                            </tbody>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="mt-2 mb-0">
                                    <button style="color: #fff;margin-left: 7px;" class="btn btn-info" type="submit"
                                        class="">Save</button>
                                    <a href="{{ route('role.index') }}" style="color: #fff;margin-left: 7px;"
                                        class="btn btn-danger" type="submit">Cancel</a>
                                </div>
                            </form>
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
            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        });
    </script>
@endsection
