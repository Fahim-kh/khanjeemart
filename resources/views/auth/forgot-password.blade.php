<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Khanjee Beauty Mart - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('admin') }}/assets/images/khanjee_icon.png" sizes="16x16" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/style.css" />
</head>

<body>
    <section class="auth forgot-password-page bg-base d-flex flex-wrap">  
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('admin') }}/assets/images/auth/forgot-pass-img.png" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <h4 class="mb-12">Forgot Password</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter the email address associated with your account and we will send you a link to reset your password.</p>
                </div>
                <form action="#">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter Email">
                    </div>
                    <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32" data-bs-toggle="modal" data-bs-target="#exampleModal">Continue</button>
    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-primary-600 fw-bold mt-24">Back to Sign In</a>
                    </div>
                    
                    <div class="mt-120 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary-600 fw-semibold">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script src="{{ asset('admin') }}/assets/js/lib/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/lib/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/lib/iconify-icon.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/app.js"></script>
</body>

</html>
