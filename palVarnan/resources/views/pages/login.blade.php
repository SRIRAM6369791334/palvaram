@extends('layouts.app')
@section('main')

<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Login</h6>
        </div>
    </div>
</div>

<section class="account py-80">
    <div class="container container-lg">

        <div class="row justify-content-center gy-4">

            <div class="col-xl-6 col-lg-7 col-md-9">

                <div class="border border-gray-100 rounded-16 px-24 py-40">

                    <!-- ================= LOGIN BOX ================= -->
                    <div id="loginBox">

                        <h6 class="text-xl mb-32 text-center">Login</h6>

                        <form id="loginForm" method="POST">
                          @csrf

                            <div class="mb-24">
                                <label>Email or Mobile</label>
                                <input type="text" name="username" id="username" class="common-input">
                            </div>

                            <div class="mb-24 position-relative">
                                <label>Password</label>

                                <input type="password" name="password" id="password" class="common-input pe-40">

                                <span class="toggle-password" data-target="password"
                                    style="position:absolute; right:12px; top:38px; cursor:pointer;">
                                    👁️
                                </span>
                            </div>

                            <button type="submit" class="btn btn-main w-100">
                                Login
                            </button>

                        </form>

                        <div class="mt-32 d-flex justify-content-between flex-wrap gap-2">

                            <a href="javascript:void(0)" id="showForgot" class="text-danger fw-semibold">
                                Forgot Password?
                            </a>

                            <p class="text-sm mb-0">
                                Don’t have an account?
                                <a href="/register" class="text-main-600 fw-semibold">
                                    Register
                                </a>
                            </p>

                        </div>

                    </div>

                    <div id="forgotBox" style="display:none;">

                        <h6 class="text-xl mb-32 text-center">Reset Password</h6>

                        <div id="resetMsg" style="display:none; padding:10px; border-radius:6px; margin-bottom:15px;"></div>

                        <form id="resetForm" method="POST">
                            @csrf

                            <div class="mb-24">
                                <label>Email</label>
                                <input type="email" name="email" id="forgotEmail" class="common-input" required>
                            </div>

                            <div class="mb-24 position-relative">
                                <label>New Password</label>

                                <input type="password" name="password" id="resetPassword" class="common-input pe-40">

                                <span class="toggle-password" data-target="resetPassword"
                                    style="position:absolute; right:12px; top:38px; cursor:pointer;">
                                    👁️
                                </span>
                            </div>

                            <button type="submit" class="btn btn-main w-100" id="resetBtn">
                                Update Password
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="javascript:void(0)" id="backToLogin">Back to Login</a>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {

    const loginBox = document.getElementById("loginBox");
    const forgotBox = document.getElementById("forgotBox");

    const showForgot = document.getElementById("showForgot");
    const backToLogin = document.getElementById("backToLogin");

    const resetForm = document.getElementById("resetForm");
    const resetBtn = resetForm.querySelector("button");
    const resetMsg = document.getElementById("resetMsg");

    let loading = false;

    // toggle
    showForgot.addEventListener("click", function () {
        loginBox.style.display = "none";
        forgotBox.style.display = "block";
    });

    backToLogin.addEventListener("click", function () {
        forgotBox.style.display = "none";
        loginBox.style.display = "block";

        resetMsg.style.display = "none";
    });

    // ================= RESET PASSWORD =================
    resetForm.addEventListener("submit", function (e) {
        e.preventDefault();

        if (loading) return;
        loading = true;

        resetBtn.disabled = true;
        resetBtn.innerHTML = "Updating...";

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

            // ❌ ERROR
            if (res.status !== 200) {

                resetMsg.style.display = "block";
                resetMsg.style.background = "#ffe0e0";
                resetMsg.style.color = "#d10000";
                resetMsg.innerHTML = res.body.errors.email
                    ? res.body.errors.email[0]
                    : "Error";

                resetBtnReset();
                return;
            }

            // ✅ SUCCESS (YOUR REQUIREMENT)
            if (res.body.success) {

                resetMsg.style.display = "block";
                resetMsg.style.background = "#d4f8d4";
                resetMsg.style.color = "#0a7a0a";
                resetMsg.innerHTML = "Password updated successfully! Redirecting to login...";

                resetForm.reset();

                setTimeout(() => {
                    forgotBox.style.display = "none";
                    loginBox.style.display = "block";
                    resetMsg.style.display = "none";
                }, 1500);
            }

            resetBtnReset();

        })
        .catch(() => {

            resetMsg.style.display = "block";
            resetMsg.style.background = "#ffe0e0";
            resetMsg.style.color = "#d10000";
            resetMsg.innerHTML = "Something went wrong";

            resetBtnReset();
        });

        function resetBtnReset() {
            loading = false;
            resetBtn.disabled = false;
            resetBtn.innerHTML = "Update Password";
        }
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const loginForm = document.getElementById("loginForm");

    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

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
                alert(res.body.errors.username[0]);
                return;
            }

            let msg = document.createElement("div");
            msg.style.background = "#d4f8d4";
            msg.style.color = "#0a7a0a";
            msg.style.padding = "10px";
            msg.style.borderRadius = "6px";
            msg.style.marginBottom = "15px";
            msg.innerHTML = "Login successful! Redirecting...";

            loginForm.prepend(msg);

            setTimeout(() => {
                window.location.href = "/";
            }, 1200);
        })
        .catch(() => {
            alert("Login failed");
        });
    });

});

document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function () {

        const target = document.getElementById(this.dataset.target);

        if (target.type === "password") {
            target.type = "text";
            this.innerHTML = "🙈";
        } else {
            target.type = "password";
            this.innerHTML = "👁️";
        }
    });
});

</script>