<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('page-title') - Khanjee beauty mart</title>
    <link rel="icon" type="image/png" href="{{ asset('admin') }}/assets/images/khanjee_icon.png" sizes="16x16" />
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/remixicon.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/bootstrap.min.css" />
    
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/dataTables.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor-katex.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor.atom-one-dark.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor.quill.snow.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/flatpickr.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/full-calendar.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/jquery-jvectormap-2.0.5.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/magnific-popup.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/slick.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/prism.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/file-upload.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/audioplayer.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/style.css" />
    
</head>
<body>
    @include('admin.layouts.include.sidebar')
    <main class="dashboard-main">
        @include('admin.layouts.include.header')
        @yield('main-content')
        <footer class="d-footer">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <p class="mb-0">© {{ date('Y') }} Khanjee Beauty Mart. All Rights Reserved.</p>
                </div>
                <div class="col-auto">
                    <p class="mb-0">Developed by <span class="text-primary-600"><a href="https://www.statelyweb.com" target="_blank">Stately Digital Solutions</a></span></p>
                </div>
            </div>
        </footer>
    </main>

<!-- jQuery library js -->
<script src="{{ asset('admin') }}/assets/js/lib/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap js -->
<script src="{{ asset('admin') }}/assets/js/lib/bootstrap.bundle.min.js"></script>
<!-- Data Table js -->
<script src="{{ asset('admin') }}/assets/js/lib/dataTables.min.js"></script>
<!-- Iconify Font js -->
<script src="{{ asset('admin') }}/assets/js/lib/iconify-icon.min.js"></script>
<!-- jQuery UI js -->
<script src="{{ asset('admin') }}/assets/js/lib/jquery-ui.min.js"></script>
<!-- Vector Map js -->
<script src="{{ asset('admin') }}/assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
<script src="{{ asset('admin') }}/assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
<!-- Popup js -->
<script src="{{ asset('admin') }}/assets/js/lib/magnifc-popup.min.js"></script>
<!-- Slick Slider js -->
<script src="{{ asset('admin') }}/assets/js/lib/slick.min.js"></script>
<!-- prism js -->
<script src="{{ asset('admin') }}/assets/js/lib/prism.js"></script>
<!-- file upload js -->
<script src="{{ asset('admin') }}/assets/js/lib/file-upload.js"></script>
<!-- audioplayer -->
<script src="{{ asset('admin') }}/assets/js/lib/audioplayer.js"></script>

<!-- main js -->
<script src="{{ asset('admin') }}/assets/js/app.js"></script>

@yield('script')

</body>

</html>
