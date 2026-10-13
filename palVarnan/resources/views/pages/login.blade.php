@extends('layouts.app')
@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Login</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        <i class="ph ph-house"></i> Home
                    </a>
                </li>
                <li class="flex-align"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600">Login</li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<section class="account py-80">
    <div class="container container-lg">
        <div class="row justify-content-center gy-4">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10">

                <div class="border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-32 py-40 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">

                    <!-- ================= LOGIN BOX ================= -->
                    <div id="loginBox">
                        <div class="text-center mb-32">
                            <span class="w-56 h-56 flex-center rounded-circle bg-main-50 text-main-600 text-28 mx-auto mb-16">
                                <i class="ph-bold ph-user-circle"></i>
                            </span>
                            <h5 class="text-24 fw-bold mb-8">Welcome Back</h5>
                            <p class="text-gray-500 text-sm mb-0">Sign in to your Pal Varnan account</p>
                        </div>

                        <div id="loginAlert" class="alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8" style="display:none;">
                            <i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i>
                            <span id="loginAlertMsg"></span>
                        </div>

                        <div id="loginSuccessAlert" class="alert alert-success py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8" style="display:none;">
                            <i class="ph-bold ph-check-circle text-lg flex-shrink-0"></i>
                            <span id="loginSuccessMsg"></span>
                        </div>

                        <form id="loginForm" action="{{ route('login.store') }}" method="POST">
                            @csrf

                            <div class="mb-20">
                                <label for="username" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    Email or Mobile Number <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" name="username" id="username" class="common-input rounded-8 ps-44" placeholder="Enter your email or phone" required>
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-envelope-simple"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-20">
                                <div class="flex-between align-items-center mb-8">
                                    <label for="password" class="text-neutral-900 text-sm fw-semibold mb-0">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                    <a href="javascript:void(0)" id="showForgot" class="text-xs text-danger-600 fw-semibold hover-text-decoration-underline">
                                        Forgot Password?
                                    </a>
                                </div>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="common-input rounded-8 ps-44 pe-44" placeholder="Enter your password" required>
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-lock-key"></i>
                                    </span>
                                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#password"></span>
                                </div>
                            </div>

                            <button type="submit" id="loginSubmitBtn" class="btn btn-main py-14 px-32 w-100 rounded-8 fw-semibold flex-center gap-8 mt-28">
                                <span>Sign In</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </button>

                        </form>

                        <div class="mt-28 pt-20 border-top border-gray-100 text-center">
                            <p class="text-sm text-gray-600 mb-0">
                                Don’t have an account yet?
                                <a href="{{ route('register') }}" class="text-main-600 fw-semibold hover-text-decoration-underline ms-4">
                                    Create Account
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- ================= FORGOT / RESET BOX ================= -->
                    <div id="forgotBox" style="display:none;">
                        <div class="text-center mb-32">
                            <span class="w-56 h-56 flex-center rounded-circle bg-main-50 text-main-600 text-28 mx-auto mb-16">
                                <i class="ph-bold ph-key"></i>
                            </span>
                            <h5 class="text-24 fw-bold mb-8">Reset Password</h5>
                            <p class="text-gray-500 text-sm mb-0">Enter your email and create a new password</p>
                        </div>

                        <div id="resetMsg" style="display:none;" class="alert py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8"></div>

                        <form id="resetForm" method="POST">
                            @csrf

                            <div class="mb-20">
                                <label for="forgotEmail" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    Registered Email <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="email" name="email" id="forgotEmail" class="common-input rounded-8 ps-44" placeholder="Enter your registered email" required>
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-envelope-simple"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-20">
                                <label for="resetPassword" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    New Password <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="resetPassword" class="common-input rounded-8 ps-44 pe-44" placeholder="Enter new password (min. 6 characters)" required minlength="6">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-lock-key"></i>
                                    </span>
                                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#resetPassword"></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-main py-14 px-32 w-100 rounded-8 fw-semibold flex-center gap-8 mt-28" id="resetBtn">
                                <span>Update Password</span>
                                <i class="ph-bold ph-check"></i>
                            </button>
                        </form>

                        <div class="mt-28 pt-20 border-top border-gray-100 text-center">
                            <a href="javascript:void(0)" id="backToLogin" class="text-sm fw-semibold text-main-600 hover-text-decoration-underline flex-center gap-6">
                                <i class="ph-bold ph-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<!-- ========================== Shipping & Trust Section Start ============================ -->
<section class="shipping mb-80" id="shipping">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="400">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2 p-20">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-car-profile"></i></span>
                    <div>
                        <h6 class="mb-4 text-base fw-bold">Free Shipping</h6>
                        <span class="text-xs text-gray-600">On qualifying orders across India</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="600">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2 p-20">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-hand-heart"></i></span>
                    <div>
                        <h6 class="mb-4 text-base fw-bold">100% Quality</h6>
                        <span class="text-xs text-gray-600">Authentic farm produce & groceries</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2 p-20">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-credit-card"></i></span>
                    <div>
                        <h6 class="mb-4 text-base fw-bold">Secure Payment</h6>
                        <span class="text-xs text-gray-600">Encrypted payments via Razorpay</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="1000">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2 p-20">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-chats"></i></span>
                    <div>
                        <h6 class="mb-4 text-base fw-bold">24/7 Support</h6>
                        <span class="text-xs text-gray-600">Dedicated phone & WhatsApp care</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================== Shipping & Trust Section End ============================ -->

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const loginBox = document.getElementById("loginBox");
    const forgotBox = document.getElementById("forgotBox");
    const showForgot = document.getElementById("showForgot");
    const backToLogin = document.getElementById("backToLogin");

    const loginForm = document.getElementById("loginForm");
    const loginBtn = document.getElementById("loginSubmitBtn");
    const loginAlert = document.getElementById("loginAlert");
    const loginAlertMsg = document.getElementById("loginAlertMsg");
    const loginSuccessAlert = document.getElementById("loginSuccessAlert");
    const loginSuccessMsg = document.getElementById("loginSuccessMsg");

    const resetForm = document.getElementById("resetForm");
    const resetBtn = document.getElementById("resetBtn");
    const resetMsg = document.getElementById("resetMsg");

    let isLoggingIn = false;
    let isResetting = false;

    // Toggle forms
    showForgot?.addEventListener("click", function () {
        loginBox.style.display = "none";
        forgotBox.style.display = "block";
        resetMsg.style.display = "none";
    });

    backToLogin?.addEventListener("click", function () {
        forgotBox.style.display = "none";
        loginBox.style.display = "block";
        loginAlert.style.display = "none";
    });

    // ================= LOGIN SUBMIT =================
    loginForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        if (isLoggingIn) return;
        isLoggingIn = true;

        loginAlert.style.display = "none";
        loginSuccessAlert.style.display = "none";

        const origBtnHtml = loginBtn.innerHTML;
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8" role="status"></span> Signing In...';

        fetch("{{ route('login.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: new FormData(loginForm)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status !== 200) {
                let errText = "Invalid login credentials. Please try again.";
                if (res.body.errors && res.body.errors.username) {
                    errText = res.body.errors.username[0];
                } else if (res.body.message) {
                    errText = res.body.message;
                }
                loginAlertMsg.textContent = errText;
                loginAlert.style.display = "flex";
                loginBtn.disabled = false;
                loginBtn.innerHTML = origBtnHtml;
                isLoggingIn = false;
                return;
            }

            loginSuccessMsg.textContent = "Login successful! Redirecting to dashboard...";
            loginSuccessAlert.style.display = "flex";
            loginBtn.innerHTML = '<i class="ph-bold ph-check me-8"></i> Success';

            setTimeout(() => {
                window.location.href = "/";
            }, 1000);
        })
        .catch(() => {
            loginAlertMsg.textContent = "An unexpected error occurred. Please try again.";
            loginAlert.style.display = "flex";
            loginBtn.disabled = false;
            loginBtn.innerHTML = origBtnHtml;
            isLoggingIn = false;
        });
    });

    // ================= RESET PASSWORD =================
    resetForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        if (isResetting) return;
        isResetting = true;

        const origResetHtml = resetBtn.innerHTML;
        resetBtn.disabled = true;
        resetBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8" role="status"></span> Updating...';

        fetch("{{ route('password.reset.direct') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: new FormData(resetForm)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status !== 200) {
                resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
                resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>' + 
                    (res.body.errors && res.body.errors.email ? res.body.errors.email[0] : (res.body.message || "Failed to update password")) + 
                    '</span>';
                resetMsg.style.display = "flex";
                resetBtn.disabled = false;
                resetBtn.innerHTML = origResetHtml;
                isResetting = false;
                return;
            }

            resetMsg.className = "alert alert-success py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-check-circle text-lg flex-shrink-0"></i> <span>Password updated successfully! Redirecting to login...</span>';
            resetMsg.style.display = "flex";
            resetForm.reset();

            setTimeout(() => {
                forgotBox.style.display = "none";
                loginBox.style.display = "block";
                resetMsg.style.display = "none";
                loginSuccessMsg.textContent = "Password reset! You can now log in with your new password.";
                loginSuccessAlert.style.display = "flex";
            }, 1500);

            resetBtn.disabled = false;
            resetBtn.innerHTML = origResetHtml;
            isResetting = false;
        })
        .catch(() => {
            resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>Network error. Please try again.</span>';
            resetMsg.style.display = "flex";
            resetBtn.disabled = false;
            resetBtn.innerHTML = origResetHtml;
            isResetting = false;
        });
    });

    // Password toggle
    document.querySelectorAll(".toggle-password").forEach(icon => {
        icon.addEventListener("click", function () {
            const target = document.querySelector(this.getAttribute("id"));
            if (!target) return;
            if (target.type === "password") {
                target.type = "text";
                this.classList.remove("ph-eye-slash");
                this.classList.add("ph-eye");
            } else {
                target.type = "password";
                this.classList.remove("ph-eye");
                this.classList.add("ph-eye-slash");
            }
        });
    });
});
</script>
@endpush