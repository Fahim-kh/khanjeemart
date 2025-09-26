<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
                {{-- <form class="navbar-search">
            <input type="text" name="search" placeholder="Search" id="searchInput">
            <iconify-icon icon="ion:search-outline" class="icon"  id="searchButton"></iconify-icon>
          </form> --}}
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a href="{{ route('download-backup') }}" class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center text-white" style="padding: 0px 42px !important; background:#db2627 !important; border-radius: 50px !important; !important;color: #fff !important;">Backup</a>
                <a href="javascript:void(0)" id="openSidebar" class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" style="padding: 0px 42px !important;border-radius: 50px !important;background: #d65353a6 !important;color: #fff !important;">Bill</a>
                <a href="{{ route('pos.index') }}" class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" style="padding: 0px 42px !important;border-radius: 50px !important;background: #487fffa6 !important;color: #fff !important;">POS</a>
                <button type="button" data-theme-toggle
                    class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>
                <div class="dropdown">
                    <button
                        class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                        type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div
                            class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
                            </div>
                            <span
                                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">{{ $headerTotalNotifications }}</span>
                        </div>

                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            @foreach($headerNotifications as $notification)
                            <a href="javascript:void(0)" 
                               class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between {{ $loop->odd ? '' : 'bg-neutral-50' }}">
                                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span class="w-44-px h-44-px rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <img src="{{ $notification->product_image 
                                        ? asset('admin/uploads/products/' . $notification->product_image) 
                                        : asset('admin/uploads/products/default.png') }}" 
                               alt="{{ $notification->product_name }}" 
                               class="w-44-px h-44-px rounded-circle object-cover">
                                    </span>
                                    <div>
                                        <h6 class="text-md fw-semibold mb-4">{{ $notification->product_name }}</h6>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">
                                            Barcode: {{ $notification->barcode }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </span>
                            </a>
                        @endforeach
                        </div>

                        <div class="text-center py-12 px-16">
                            <a href="{{ route('notifications') }}" class="text-primary-600 fw-semibold text-md">See All
                                Notification</a>
                        </div>

                    </div>
                </div><!-- Notification dropdown end -->

                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        @php
                            $user = Auth::user();
                            $image = asset('admin/assets/images/blur.avif');
                            $user_image = asset('admin/uploads/user_images/' . $user->user_image);
                        @endphp
                        <img src="{{ $user->user_image != null ? $user_image : $image }}" alt="image"
                            class="w-40-px h-40-px object-fit-cover rounded-circle">
                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2">{{ Auth::user()->name }}</h6>
                                <span
                                    class="text-secondary-light fw-medium text-sm">{{ Auth::user()->role->name }}</span>
                            </div>
                            <button type="button" class="hover-text-danger">
                                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                            </button>
                        </div>
                        <ul class="to-top-list">
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="{{ route('user.edit', Auth::user()->id) }}">
                                    <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon> My
                                    Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="{{ route('change_password') }}">
                                    <iconify-icon icon="icon-park-outline:setting-two"
                                        class="icon text-xl"></iconify-icon> Setting</a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 border-0 bg-transparent w-100 text-start">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                        Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div><!-- Profile dropdown end -->
            </div>
        </div>
    </div>
</div>
