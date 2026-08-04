@extends('layouts.app')

@section('main')

<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Register</h6>
        </div>
    </div>
</div>

<section class="account py-80">
    <div class="container container-lg">

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="row justify-content-center gy-4">

                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="border border-gray-100 rounded-16 px-24 py-40">

                        <h6 class="text-xl mb-32 text-center">Register</h6>

                        <div class="mb-24">
                            <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                First Name <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="name" id="name" class="common-input" placeholder="Enter First Name" required>
                        </div>

                        <div class="mb-24">
                            <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                Last Name <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="last_name" id="last_name" class="common-input" placeholder="Enter Last Name" >
                        </div>

                       

                        <div class="mb-24">
                            <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                Email <span class="text-danger">*</span>
                            </label>

                            <input type="email" name="email" id="email" class="common-input" placeholder="Enter Email" >
                        </div>

                        <div class="mb-24">
                            <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                Mobile Number <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="phone_number" id="phone_number" class="common-input" placeholder="Enter Mobile Number" >
                        </div>

                        <div class="mb-24">
                            <label class="text-neutral-900 text-lg mb-8 fw-medium">
                                Password <span class="text-danger">*</span>
                            </label>

                            <input type="password" name="password"id="password" class="common-input" placeholder="Enter Password" >
                        </div>

                        <div class="mt-48 text-center">
                            <button type="submit" class="btn btn-main py-18 px-40">
                                Register
                            </button>
                        </div>

                        <div class="mt-24 text-center">
                            <p class="mb-0 text-sm">
                                Already have an account?
                                <a href="/login"
                                   class="text-main-600 fw-semibold">
                                    Login
                                </a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </div>
</section>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {

    let isSubmitting = false;
    const form = document.querySelector("form");

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    function addRequiredValidation(id, name) {
        const input = document.getElementById(id);

        input.addEventListener("blur", function () {
            if (input.value.trim() === "") {
                showError(id, name + " is required");
            }
        });

        input.addEventListener("input", function () {
            clearError(id);
        });
    }

    // Fields
    addRequiredValidation("name", "First Name");
    addRequiredValidation("last_name", "Last Name");
    addRequiredValidation("email", "Email");
    addRequiredValidation("phone_number", "Mobile Number");
    addRequiredValidation("password", "Password");

    // Email validation
    document.getElementById("email").addEventListener("input", function () {
        let email = this.value.trim();
        let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email !== "" && !regex.test(email)) {
            showError("email", "Enter valid email");
        } else {
            clearError("email");
        }
    });

    // Phone validation
    document.getElementById("phone_number").addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");

        if (this.value.length > 0 && this.value.length < 10) {
            showError("phone_number", "Phone must be at least 10 digits");
        } else {
            clearError("phone_number");
        }
    });

form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (isSubmitting) return;
    isSubmitting = true;

    clearAllErrors();

    // disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Submitting... <i class="fas fa-spinner fa-spin"></i>';

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

        // ❌ validation errors
        if (!res.ok) {

            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showError(field, data.errors[field][0]);
                });
            }

            resetButton();
            return;
        }

        // ✅ success
        if (data.success) {

            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message,
                confirmButtonColor: '#026966'
            });

            form.reset();

            setTimeout(() => {
                resetButton();
            }, 1500);

            return;
        }

        resetButton();

    })
    .catch(() => {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong'
        });

        resetButton();
    });
});

    function showError(field, message) {
        let input = document.getElementById(field);
        clearError(field);

        let span = document.createElement("span");
        span.className = "ajax-error";
        span.style.color = "red";
        span.style.fontSize = "13px";
        span.textContent = message;

        input.parentNode.appendChild(span);
    }

    function clearError(field) {
        let input = document.getElementById(field);
        let error = input.parentNode.querySelector(".ajax-error");
        if (error) error.remove();
    }

    function clearAllErrors() {
        document.querySelectorAll(".ajax-error").forEach(e => e.remove());
    }

    function resetButton() {
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }

});
</script>