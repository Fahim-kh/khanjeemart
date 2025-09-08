@extends('admin.layouts.master')

@section('page-title')
    Expense Categories
@endsection
@section('main-content')
<style>
    .select2-container, .select2-selection, .select2-dropdown {
    width: 760.5px !important;
}
</style>
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0"> Expense Categories List</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                         Expense Categories List
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
                            @if (isset(Auth::user()->hasPer('Expense Categories')['pcreate']) && Auth::user()->hasPer('Expense Categories')['pcreate'] == 1)
                                <button type="button" class="btn btn-success create">Add New Expense</button>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table bordered-table mb-0" id="example" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-start">S No</th>
                                        <th scope="col">Expense Category</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                 </thead>
                            </table>
                        </div>

                        
                        <div class="modal fade modalAdd" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalAddLabel">New Expense Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <!-- Form -->
                                    <form id="expense">
                                        @include('admin.layouts.validationLayout')
                                        
                                        <div class="modal-body">
                                            <input type="hidden" name="id" class="id">
                                            
                                            <!-- Name Input -->
                                          
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name *</label>
                                                <input type="text" class="form-control name" name="name" id="name" required>
                                            </div>

                                            
                                            
                                            @include('admin.layouts.status')
                                            
                                        </div>
                                        
                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <button type="submit" id="btnSave" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>



                        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        Do you want to delete this record?
                                    </div>
                                    
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="btnDelete" class="btn btn-danger">Delete</button>
                                    </div>
                                </div>
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
    const loadExpenseCategory_route = "{{ route('loadExpenseCategory') }}";
    const expense_category_store = "{{ route('expense_category.store') }}";
</script>
<script src="{{ asset('admin/myjs/expense/expense_category.js') }}"></script>
<script>

</script>
@endsection
