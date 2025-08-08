<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .select2-container,
        .select2-selection,
        .select2-dropdown {
            width: 510.5px !important;
        }
        th{
            font-size: 14px;
            font-weight: 400;
        }
    </style>
</head>

<body>
    @include('admin.layouts.include.sidebar')
    <main class="dashboard-main">
        @include('admin.layouts.include.header')
        @yield('main-content')
        <!-- Barcode Scan Modal -->
        <div class="modal fade" id="barcodeScanModal" tabindex="-1" aria-labelledby="barcodeScanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="barcodeScanModalLabel">Scan Barcode</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="scanner-container" style="width: 100%; height: 300px; border: 2px dashed #ddd;">
                            <video id="video" style="width: 100%; height: 300px;"></video>
                        </div>
                        <p class="text-muted mt-3" id="result">Or enter barcode manually:</p>
                        <button type="button" id="startButton"  class="btn btn-success ">Start Scanning</button>
                        <button type="button"  id="stopButton" class="btn btn-secondary" data-bs-dismiss="modal" >Stop Scanning</button>
                    </div>
                </div>
            </div>
        </div>


        <footer class="d-footer">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <p class="mb-0">© {{ date('Y') }} Khanjee Beauty Mart. All Rights Reserved.</p>
                </div>
                <div class="col-auto">
                    <p class="mb-0">Developed by <span class="text-primary-600"><a href="https://www.statelyweb.com"
                                target="_blank">Stately Digital Solutions</a></span></p>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="{{ asset('admin/myjs/mylib.js') }}"></script>
    <!-- Toastr JS (place just before closing </body> tag or in your JS stack) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        const parentWidth = 510.5;
        $.fn.select2.defaults.set("theme", "bootstrap-5");
        $.fn.select2.defaults.set("width", parentWidth + "px");

        const codeReader = new ZXing.BrowserMultiFormatReader();
        let scannerActive = false;

        document.getElementById('startButton').addEventListener('click', () => {
            if (scannerActive) return;

            scannerActive = true;
            document.getElementById('startButton').disabled = true;
            document.getElementById('stopButton').disabled = false;
            document.getElementById('result').textContent = 'Scanning...';

            codeReader.decodeFromVideoDevice(null, 'video', (result, err) => {
                if (result) {
                    document.getElementById('result').textContent = result.text;
                    document.getElementById('code').value = result.text;

                    beep();

                    // Stop scanning and close modal
                    codeReader.reset();
                    scannerActive = false;
                    document.getElementById('startButton').disabled = false;
                    document.getElementById('stopButton').disabled = true;

                    // Hide modal (Bootstrap 5)
                    const barcodeModal = bootstrap.Modal.getInstance(document.getElementById(
                        'barcodeScanModal'));
                    barcodeModal.hide();
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.error(err);
                    document.getElementById('result').textContent = 'Error: ' + err;
                }
            });
        });

        document.getElementById('stopButton').addEventListener('click', () => {
            if (!scannerActive) return;

            codeReader.reset();
            scannerActive = false;
            document.getElementById('startButton').disabled = false;
            document.getElementById('stopButton').disabled = true;
            document.getElementById('result').textContent = 'Scanner stopped';
        });

    // Simple beep function (optional)
        function beep() {
            const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            oscillator.type = 'sine';
            oscillator.frequency.value = 800;
            gainNode.gain.value = 0.1;

            oscillator.start();
            setTimeout(() => {
                oscillator.stop();
            }, 100);
        }
        $(document).on('keydown', function (e) {
            // Ctrl + S
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#searchButton').trigger('click');
            }

            // Alt + S
            if (e.altKey && e.key.toLowerCase() === 's') {
                e.preventDefault();
                $('#searchButton').trigger('click');
            }

            // Just press `/` key
            if (e.key === '/') {
                e.preventDefault();
                $('.dt-input').focus(); // if you want to focus a search input field
            }
            // F2 key
            if (e.key === "F2") {
                alert('dd');
                e.preventDefault(); // prevent default browser behavior
                $('#searchButton').trigger('click');
            }
        });
        $(document).ready(function () {
            toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
            };
        });
    </script>
    @yield('script')

</body>

</html>
