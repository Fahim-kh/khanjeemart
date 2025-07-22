@extends('admin.layouts.master')

@section('page-title')
    Add User
@endsection
<style>
    .form-group {
        margin-top: 20px;
    }

    .upload-btn {
        margin-top: 20px;
        border: 1.5px solid gray;
        color: gray;
        background-color: white;
        padding: 3px 13px;
        border-radius: 8px;
        font-size: 15px;
        cursor: pointer !important;
    }

    .stff-tming-buttns {
        padding: 10px;
        text-align: right;
        background: #0000001c;
        border-bottom-right-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .save-btn {
        width: 100px;
        padding: 8px 13px;
        text-align: center;
        color: white;
        background: #0F0A32 !important;
        border: none;
        font-size: 14px;
        border-radius: 100px;
        box-shadow: 0px 32px 23px -24px rgba(10, 177, 167, 0.55) !important;
    }

    .bg_file {
        background: #eeee;
        padding: 10px;
    }

    .btn-info {
        background: #0F0A32 !important;
    }

    .addRow {
        background: #0F0A32 !important;
    }

    .select2-container {
        width: 100% !important;
    }
    .selection{
        width:243px;
    }
    .select2-container--bootstrap-5 .select2-selection {
    height: 56px;
    border-radius: 12px;
    padding: 0.5rem 1rem !important;
    font-size: 1rem;
}


</style>
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Create User</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Create User
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

                            <div class="container popup-tabs model_body">
                                <form action="{{ route('module.update',['id'=>$module->id]) }}" method="POST" id="moduleForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="module_name">Module Name</label>
                                        <input type="text" class="form-control" id="module_name" name="name" value={{ $module->name }}>
                                    </div>
                                    <div class="form-group">
                                        <label for="module_route">Module Route (put # for empty route)</label>
                                        <input type="text" class="form-control" id="module_route" name="route" value="{{ $module->route }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="parent_module">Parent Module</label>
                                        <select name="parent_id" id="parent_module" class="form-control">
                                            <option selected disabled>Please Select Parent Module</option>
                                            @foreach ($modules as $value)
                                                {{-- <option value="{{ $value->id }}">{{ $value->name }}</option> --}}
                                                <option value="{{ $value->id }}" {{ $module->parent_id==$value->id?"selected":"" }}>{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="module_icon">Module Icon</label>
                                        <input type="text" class="form-control" id="module_icon" name="icon" value="{{ $module->icon }}" placeholder="e.g. fa fa-home">
                                    </div>
                                    <div class="form-group">
                                        <label for="module_sort">Module Sort</label>
                                        <input type="number" class="form-control" id="module_sort"min="1" name="sort" value="{{ $module->sorting }}" placeholder="e.g. 1">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success" id="btn-submit">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
       
    </script>
@endsection
