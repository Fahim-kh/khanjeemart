@extends('admin.layouts.master')

@section('page-title')
    Change Password
@endsection
@section('main-content')
    <div class="dashboard-main-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Change Password</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Change Password
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Dashboard</li>
            </ul>
        </div>

        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="{{ route('passwordChange') }}" id="UserFormCreate"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group" style="margin-top:20px;">
                                    <label>Current Password</label>
                                    <input type="password" name="old_password"
                                        class="form-control @error('old_password') is-invalid @enderror"
                                        placeholder="Enter old password">
                                    @error('old_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <label>Password</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <label>Confirm Password</label>
                                    <input type="password" name="retype_password"
                                        class="form-control @error('retype_password') is-invalid @enderror"
                                        placeholder="Retype-Password">
                                    @error('retype_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group" style="margin-top:20px;">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-danger">Cancle</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
