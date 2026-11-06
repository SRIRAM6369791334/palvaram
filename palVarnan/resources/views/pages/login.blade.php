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
                            <p class="text-gray-500 text-sm mb-0" id="resetSubheading">Enter your email to receive a 6-digit verification code</p>
                        </div>

                        <div id="resetMsg" style="display:none;" class="alert py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8"></div>

                        <!-- Step 1: Send OTP -->
                        <form id="sendOtpForm" method="POST">
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

                            <button type="submit" class="btn btn-main py-14 px-32 w-100 rounded-8 fw-semibold flex-center gap-8 mt-28" id="sendOtpBtn">
                                <span>Send Verification Code</span>
                                <i class="ph-bold ph-paper-plane-tilt"></i>
                            </button>
                        </form>

                        <!-- Step 2: Verify OTP & Reset Password -->
                        <form id="verifyOtpForm" method="POST" style="display:none;">
                            @csrf
                            <input type="hidden" name="email" id="verifiedEmail">

                            <div class="mb-16 p-12 bg-main-50 border border-main-200 rounded-8 flex-between">
                                <span class="text-xs text-gray-600">Code sent to: <strong id="sentEmailDisplay" class="text-main-600"></strong></span>
                                <button type="button" id="changeEmailBtn" class="border-0 bg-transparent text-xs text-main-600 fw-semibold text-decoration-underline p-0">Change</button>
                            </div>

                            <div class="mb-20">
                                <label for="otpCode" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    6-Digit Verification Code <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" name="otp" id="otpCode" class="common-input rounded-8 ps-44" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}" required style="letter-spacing: 4px; font-weight: 600;">
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-shield-check"></i>
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

                            <button type="submit" class="btn btn-main py-14 px-32 w-100 rounded-8 fw-semibold flex-center gap-8 mt-28" id="verifyOtpBtn">
                                <span>Verify & Update Password</span>
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

    const sendOtpForm = document.getElementById("sendOtpForm");
    const sendOtpBtn = document.getElementById("sendOtpBtn");
    const verifyOtpForm = document.getElementById("verifyOtpForm");
    const verifyOtpBtn = document.getElementById("verifyOtpBtn");
    const forgotEmail = document.getElementById("forgotEmail");
    const verifiedEmail = document.getElementById("verifiedEmail");
    const sentEmailDisplay = document.getElementById("sentEmailDisplay");
    const changeEmailBtn = document.getElementById("changeEmailBtn");
    const resetMsg = document.getElementById("resetMsg");
    const resetSubheading = document.getElementById("resetSubheading");

    let isLoggingIn = false;
    let isSendingOtp = false;
    let isVerifyingOtp = false;

    // Toggle forms
    showForgot?.addEventListener("click", function () {
        loginBox.style.display = "none";
        forgotBox.style.display = "block";
        sendOtpForm.style.display = "block";
        verifyOtpForm.style.display = "none";
        resetMsg.style.display = "none";
        resetSubheading.textContent = "Enter your email to receive a 6-digit verification code";
    });

    changeEmailBtn?.addEventListener("click", function () {
        sendOtpForm.style.display = "block";
        verifyOtpForm.style.display = "none";
        resetMsg.style.display = "none";
        resetSubheading.textContent = "Enter your email to receive a 6-digit verification code";
    });

    backToLogin?.addEventListener("click", function () {
        forgotBox.style.display = "none";
        loginBox.style.display = "block";
        loginAlert.style.display = "none";
        resetMsg.style.display = "none";
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

        const urlParams = new URLSearchParams(window.location.search);
        const redirectParam = urlParams.get('redirect');
        const loginData = new FormData(loginForm);
        if (redirectParam) {
            loginData.append('redirect', redirectParam);
        }

        fetch("{{ route('login.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: loginData
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

            const targetUrl = redirectParam || res.body.redirect_url || "/";
            loginSuccessMsg.textContent = "Login successful! Redirecting...";
            loginSuccessAlert.style.display = "flex";
            loginBtn.innerHTML = '<i class="ph-bold ph-check me-8"></i> Success';

            setTimeout(() => {
                window.location.href = targetUrl;
            }, 800);
        })
        .catch(() => {
            loginAlertMsg.textContent = "An unexpected error occurred. Please try again.";
            loginAlert.style.display = "flex";
            loginBtn.disabled = false;
            loginBtn.innerHTML = origBtnHtml;
            isLoggingIn = false;
        });
    });

    // ================= STEP 1: SEND RESET OTP =================
    sendOtpForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        if (isSendingOtp) return;
        isSendingOtp = true;

        const origHtml = sendOtpBtn.innerHTML;
        sendOtpBtn.disabled = true;
        sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8" role="status"></span> Sending Code...';
        resetMsg.style.display = "none";

        fetch("{{ route('password.sendOtp') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: new FormData(sendOtpForm)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status !== 200) {
                resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
                resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>' + 
                    (res.body.errors && res.body.errors.email ? res.body.errors.email[0] : (res.body.message || "Failed to send verification code")) + 
                    '</span>';
                resetMsg.style.display = "flex";
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = origHtml;
                isSendingOtp = false;
                return;
            }

            const emailVal = forgotEmail.value.trim();
            verifiedEmail.value = emailVal;
            sentEmailDisplay.textContent = emailVal;

            sendOtpForm.style.display = "none";
            verifyOtpForm.style.display = "block";
            resetSubheading.textContent = "Enter the 6-digit verification code sent to your email";

            resetMsg.className = "alert alert-success py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-check-circle text-lg flex-shrink-0"></i> <span>' + (res.body.message || "Verification code sent to your email.") + '</span>';
            resetMsg.style.display = "flex";

            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = origHtml;
            isSendingOtp = false;
            document.getElementById("otpCode").focus();
        })
        .catch(() => {
            resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>Network error. Please try again.</span>';
            resetMsg.style.display = "flex";
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerHTML = origHtml;
            isSendingOtp = false;
        });
    });

    // ================= STEP 2: VERIFY OTP & RESET PASSWORD =================
    verifyOtpForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        if (isVerifyingOtp) return;
        isVerifyingOtp = true;

        const origHtml = verifyOtpBtn.innerHTML;
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8" role="status"></span> Updating...';
        resetMsg.style.display = "none";

        fetch("{{ route('password.resetWithOtp') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: new FormData(verifyOtpForm)
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status !== 200) {
                const errMsg = res.body.errors 
                    ? (res.body.errors.otp ? res.body.errors.otp[0] : (res.body.errors.password ? res.body.errors.password[0] : (res.body.errors.email ? res.body.errors.email[0] : "Verification failed")))
                    : (res.body.message || "Failed to update password");

                resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
                resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>' + errMsg + '</span>';
                resetMsg.style.display = "flex";
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = origHtml;
                isVerifyingOtp = false;
                return;
            }

            resetMsg.className = "alert alert-success py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-check-circle text-lg flex-shrink-0"></i> <span>Password reset successfully! Redirecting to login...</span>';
            resetMsg.style.display = "flex";
            verifyOtpForm.reset();

            setTimeout(() => {
                forgotBox.style.display = "none";
                loginBox.style.display = "block";
                resetMsg.style.display = "none";
                loginSuccessMsg.textContent = "Password reset! You can now log in with your new password.";
                loginSuccessAlert.style.display = "flex";
            }, 1800);

            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = origHtml;
            isVerifyingOtp = false;
        })
        .catch(() => {
            resetMsg.className = "alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8";
            resetMsg.innerHTML = '<i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i> <span>Network error. Please try again.</span>';
            resetMsg.style.display = "flex";
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = origHtml;
            isVerifyingOtp = false;
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