@extends('admin.layouts.master')

@section('page-title')
    Notifications
@endsection
@section('main-content')
    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Notifications</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="#" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Notifications
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Notifications</li>
            </ul>
        </div>
      <div class="card">
        <div class="card-body">
            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                @foreach($notifications as $notification)
                    <a href="javascript:void(0)" 
                       class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between {{ $loop->odd ? '' : 'bg-neutral-50' }}">
                       
                        <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                            <span class="w-44-px h-44-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                <img src="{{ $notification->product_image 
                                ? asset('admin/uploads/products/' . $notification->product_image) 
                                : asset('admin/uploads/products/default.png') }}" >
                            </span>
            
                            <div>
                                <h6 class="text-md fw-semibold mb-4">{{ $notification->product_name }} - <span class="bg-warning p-1">Out of Stock</span></h6>
                                <p class="mb-0 text-sm text-secondary-light text-w-200-px">
                                    Barcode: {{ $notification->barcode }}
                                </p>
                            </div>
                        </div>
            
                        <span class="text-sm text-secondary-light flex-shrink-0">
                            {{ \Carbon\Carbon::parse($notification->notification_time)->diffForHumans() }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
      </div>
    </div>
@endsection
@section('script')
@endsection
