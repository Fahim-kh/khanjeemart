<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Khanjee Beauty Mart - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('admin') }}/assets/images/khanjee_icon.png" sizes="16x16" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/style.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/remixicon.css" />

</head>

<body>
    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('admin') }}/assets/images/auth/auth-img.png" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100" id="loginBox">
                <div>
                    <a href="index.html" class="mb-40 max-w-290-px">
                        <img src="{{ asset('admin') }}/assets/images/khanjee_logo.png" alt="logo">
                    </a>
                    <h4 class="mb-12">Sign In to your Account</h4>
                    <p class="mb-32 text-secondary-light text-lg">Welcome back! please enter your detail</p>
                    @if (Session::has('error'))
                        <div class="badge bg-danger flash-message" role="alert">{{ Session::get('error') }}</div>
                    @endif
                </div>
                <form  action="{{ route('attempt.login') }}" id="loginForm">
                    <div class="icon-field mb-16 email">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email"
                            class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Email">
                        
                    </div>
                    @if ($errors->has('email'))
                            <div class="badge bg-danger flash-message">
                                {{ $errors->first('email') }}
                            </div>
                    @endif
                    <div class="position-relative mb-20 password">
                        <div class="icon-field">
                            <span class="icon top-50 translate-middle-y">
                                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                            </span>
                            <input type="password" name="password"
                                class="form-control h-56-px bg-neutral-50 radius-12 @error('password') is-invalid @enderror"
                                id="your-password" placeholder="Password">
                        </div>
                        <span
                            class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                            data-toggle="#your-password"></span>
                    </div>
                    @if ($errors->has('password'))
                            <div class="badge bg-danger flash-message">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                    <div class="">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input border border-neutral-300" name="remember"
                                    type="checkbox" id="remeber">
                                <label class="form-check-label" for="remeber">Remember me </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-primary-600 fw-medium">Forgot
                                Password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">
                        Sign
                        In</button>
                </form>
                <div class="message mt-3"></div>
            </div>
        </div>
    </section>
    <script src="{{ asset('admin') }}/assets/js/lib/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/lib/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/lib/iconify-icon.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/app.js"></script>
    <script>
        initializePasswordToggle('.toggle-password');
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let formMain = $('#loginBox');
                let formData = form.serialize();
                console.log(formData);


                // Remove previous error messages
                $('.flash-message').remove();
                $('.message').html('');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.status == 'success'){
                            var html='<span class="form-control p-3 bg-success text-white" style="color:#fff !important;">LoggedIn!</span>';
                            $('.message').html(html);
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 2000);
                        }
                        if(response.status =='success'){
                          
                         
                        } else if(response.status =="fail"){
                           var html='<span class="form-control p-3 bg-danger text-white">Invalid email or password!</span>';
                           $('.message').html(html);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;

                        $('input[name="email"], input[name="password"]').removeClass('is-invalid');

                        if (errors) {
                            if (errors.email) {
                                $('input[name="email"]').addClass('is-invalid');
                            }
                            if (errors.password) {
                                $('input[name="password"]').addClass('is-invalid');
                            }
                        } else {
                            alert('Login failed. Please try again.');
                        }
                    }
                });
            });
        });

        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on('click', function() {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
    </script>

</body>

</html>
