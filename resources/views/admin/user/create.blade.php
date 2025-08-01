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
                            <form method="post" action="{{ route('user.store') }}" id="staffCreate"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                        <div class="upload-ppic upload-ppic-ext">
                                            <center>
                                                <!-- Upload Image Start -->
                                                <div class="mb-24 mt-16">
                                                    <div class="avatar-upload">
                                                        <div
                                                            class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                                            <input type='file' name="user_image" id="imageUpload"
                                                                accept=".png, .jpg, .jpeg" hidden>
                                                            <label for="imageUpload"
                                                                class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                                                <iconify-icon icon="solar:camera-outline"
                                                                    class="icon"></iconify-icon>
                                                            </label>
                                                        </div>
                                                        <div class="avatar-preview">
                                                            <div id="imagePreview"> </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Upload Image End -->
                                            </center>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="name">Full Name*</label>
                                                    <input type="text" name="name" id="name"
                                                        class="form-control name @error('name')  is-invalid state-invalid @enderror"
                                                        placeholder="Enter Name" onkeydown="return alphaOnly(event)">
                                                    @if ($errors->has('name'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('name') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email">Email*</label>
                                                    <input type="email" name="email" id="email"
                                                        class="form-control email @error('email') is-invalid state-invalid @enderror"
                                                        placeholder="Enter Email Address">
                                                    @if ($errors->has('email'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('email') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="phone_number">Phone Number</label>
                                                    <input type="tel" name="phone_number" id="phone_number"
                                                        class="form-control phone_number @error('phone_number') is-invalid state-invalid @enderror"
                                                        placeholder="Enter Phone Number ">
                                                    @if ($errors->has('phone_number'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('phone_number') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="address">Address</label>
                                                    <input type="text" name="address" id="address"
                                                        class="form-control address @error('address') is-invalid state-invalid @enderror"
                                                        placeholder="Enter Address ">
                                                    @if ($errors->has('address'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('address') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group w-100">
                                                    <label for="role_id">Role</label>
                                                    <select name="role_id" id="role_id"
                                                        class="form-control role_id @error('role_id') is-invalid @enderror" style="width:100% !important;">
                                                        <option></option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                                {{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('role_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control @error('password')  is-invalid state-invalid @enderror"
                                                        placeholder="Enter password">
                                                    @if ($errors->has('password'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('password') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="retype_password">Retype Password</label>
                                                    <input type="password" name="retype_password" id="retype_password"
                                                        class="form-control @error('retype_password')  is-invalid state-invalid @enderror"
                                                        placeholder="Enter retype password">
                                                    @if ($errors->has('retype_password'))
                                                        <div class="invalid-feedback" role="alert">
                                                            {{ $errors->first('retype_password') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" id="status" class="form-control">
                                                        <option value="0">Deactive</option>
                                                        <option value="1">Active</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="stff-tming-buttns" style="margin-top:20px;">
                                    <button id="addstafftiming_save" class="save-btn msg-send-btn">Save</button>
                                    <a href="{{ route('user.index') }}" class="btn btn-danger close-btn"
                                        type="button">Back</a>
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
        $('.role_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Select Role",
            allowClear: false,
            width: '100%'
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
    </script>
@endsection
