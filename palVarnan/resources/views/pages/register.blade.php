@extends('layouts.app')

@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Register</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        <i class="ph ph-house"></i> Home
                    </a>
                </li>
                <li class="flex-align"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600">Register</li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<section class="account py-80">
    <div class="container container-lg">
        <div class="row justify-content-center gy-4">
            <div class="col-xl-6 col-lg-7 col-md-9 col-sm-11">

                <div class="border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-32 py-40 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">

                    <div class="text-center mb-32">
                        <span class="w-56 h-56 flex-center rounded-circle bg-main-50 text-main-600 text-28 mx-auto mb-16">
                            <i class="ph-bold ph-user-plus"></i>
                        </span>
                        <h5 class="text-24 fw-bold mb-8">Create an Account</h5>
                        <p class="text-gray-500 text-sm mb-0">Join Pal Varnan for fresh farm groceries & easy checkout</p>
                    </div>

                    <div id="registerAlert" class="alert alert-danger py-10 px-16 text-sm mb-20 rounded-8 flex-align gap-8" style="display:none;">
                        <i class="ph-bold ph-warning-circle text-lg flex-shrink-0"></i>
                        <span id="registerAlertMsg"></span>
                    </div>

                    <form id="registerForm" action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <!-- First & Last Name row -->
                        <div class="row g-16 mb-20">
                            <div class="col-sm-6">
                                <label for="name" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" name="name" id="name" class="common-input rounded-8 ps-44" placeholder="First Name" required>
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-user"></i>
                                    </span>
                                </div>
                                <span class="field-error text-danger text-xs mt-4 d-block" id="err-name"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="last_name" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" name="last_name" id="last_name" class="common-input rounded-8 ps-44" placeholder="Last Name" required>
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                        <i class="ph ph-user"></i>
                                    </span>
                                </div>
                                <span class="field-error text-danger text-xs mt-4 d-block" id="err-last_name"></span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-20">
                            <label for="email" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="email" name="email" id="email" class="common-input rounded-8 ps-44" placeholder="example@domain.com" required>
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                    <i class="ph ph-envelope-simple"></i>
                                </span>
                            </div>
                            <span class="field-error text-danger text-xs mt-4 d-block" id="err-email"></span>
                        </div>

                        <!-- Mobile Number -->
                        <div class="mb-20">
                            <label for="phone_number" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                Mobile Number <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="tel" name="phone_number" id="phone_number" class="common-input rounded-8 ps-44" placeholder="10-digit mobile number" required maxlength="10">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                    <i class="ph ph-phone"></i>
                                </span>
                            </div>
                            <span class="field-error text-danger text-xs mt-4 d-block" id="err-phone_number"></span>
                        </div>

                        <!-- Password -->
                        <div class="mb-24">
                            <label for="password" class="text-neutral-900 text-sm mb-8 fw-semibold d-block">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password" class="common-input rounded-8 ps-44 pe-44" placeholder="Minimum 6 characters" required minlength="6">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                    <i class="ph ph-lock-key"></i>
                                </span>
                                <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#password"></span>
                            </div>
                            <span class="field-error text-danger text-xs mt-4 d-block" id="err-password"></span>
                        </div>

                        <!-- Terms notice -->
                        <div class="mb-24">
                            <p class="text-gray-500 text-xs mb-0 lh-base">
                                By signing up, you agree to Pal Varnan's 
                                <a href="/terms-conditions" class="text-main-600 fw-medium hover-text-decoration-underline">Terms of Service</a> 
                                and 
                                <a href="/privacy-policy" class="text-main-600 fw-medium hover-text-decoration-underline">Privacy Policy</a>.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="registerSubmitBtn" class="btn btn-main py-14 px-32 w-100 rounded-8 fw-semibold flex-center gap-8">
                            <span>Register Account</span>
                            <i class="ph-bold ph-arrow-right"></i>
                        </button>

                    </form>

                    <div class="mt-28 pt-20 border-top border-gray-100 text-center">
                        <p class="text-sm text-gray-600 mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-main-600 fw-semibold hover-text-decoration-underline ms-4">
                                Sign In
                            </a>
                        </p>
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
    let isSubmitting = false;
    const form = document.getElementById("registerForm");
    const submitBtn = document.getElementById("registerSubmitBtn");
    const origBtnHtml = submitBtn.innerHTML;
    const registerAlert = document.getElementById("registerAlert");
    const registerAlertMsg = document.getElementById("registerAlertMsg");

    function clearAllErrors() {
        document.querySelectorAll(".field-error").forEach(el => el.textContent = "");
        registerAlert.style.display = "none";
    }

    function showFieldError(field, msg) {
        const el = document.getElementById("err-" + field);
        if (el) el.textContent = msg;
    }

    // Phone format
    document.getElementById("phone_number")?.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    // Form submit
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        clearAllErrors();
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-8" role="status"></span> Creating Account...';

        let formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async res => {
            let data = await res.json();

            if (!res.ok) {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        showFieldError(field, data.errors[field][0]);
                    });
                } else if (data.message) {
                    registerAlertMsg.textContent = data.message;
                    registerAlert.style.display = "flex";
                }
                resetButton();
                return;
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Account Created!',
                    text: data.message || 'Registration successful! You can now log in.',
                    confirmButtonColor: '#1c799b'
                }).then(() => {
                    window.location.href = "{{ route('login') }}";
                });

                form.reset();
                setTimeout(() => {
                    window.location.href = "{{ route('login') }}";
                }, 2000);
            } else {
                resetButton();
            }
        })
        .catch(() => {
            registerAlertMsg.textContent = "An error occurred while creating your account. Please try again.";
            registerAlert.style.display = "flex";
            resetButton();
        });
    });

    function resetButton() {
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = origBtnHtml;
    }

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