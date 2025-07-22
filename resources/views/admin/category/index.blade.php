@extends('admin.layouts.master')

@section('page-title')
    Setup Category Form
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Category List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Category List
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>
        <div class="container">
            @include('admin.layouts.errorLayout')
            @include('admin.layouts.successLayout')
            <div class="row justify-content-center ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                             <button type="button" id="delete_all_record" url="category/deleteAll"
                    class="btn btn-danger delete_all">Delete</button>
                    
                            @if (isset(Auth::user()->hasPer('Category')['pcreate']) && Auth::user()->hasPer('Category')['pcreate'] == 1)
                                <button type="button" class="btn btn-success create">Add New Category</button>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="chk_select_all" name="form-check-input chk_select_all"
                                                value="ALL" />
                                        </th>
                                        <th class="text-left">S No</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                 </thead>
                            </table>
                        </div>

                        
                        <div class="modal fade modalAdd" id="modalAdd" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="example-Modal3">New message</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="category">
                                        @include('admin.layouts.validationLayout')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="hidden" name="id" class="id">
                                                <label for="name" class="form-control-label">Name</label>
                                                <input type="text" class="form-control name" name="name"
                                                    placeholder="Enter Name" required>
                                            </div>
                                            @include('admin.layouts.status')

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Confirm Delete</h4>
                                    </div>
                                    <div class="modal-body">
                                        Do you want to delete this record?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ asset('admin/myjs/category/category.js') }}"></script>
@endsection
