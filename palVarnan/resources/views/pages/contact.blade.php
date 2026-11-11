@extends('layouts.app')

@section('main')

@php
    $currentCountryId = session('country_id');
    $currentCountry = $currentCountryId ? \App\Models\Country::find($currentCountryId) : null;
    $isMexico = ($currentCountry && strtolower($currentCountry->name) === 'mexico') || ($currentCountryId == 244);
    $defaultCountry = $isMexico ? 'mexico' : 'india';
@endphp

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">Contact Us</h6>
            <ul class="flex-align gap-8 flex-wrap list-unstyled mb-0">
                <li class="text-sm">
                    <a href="{{ url('/') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600 text-decoration-none">
                        <i class="ph ph-house"></i>
                        Home
                    </a>
                </li>
                <li class="flex-align text-neutral-400">
                    <i class="ph ph-caret-right"></i>
                </li>
                <li class="text-sm text-main-600 fw-semibold"> Contact Us </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- ============================ Contact Section Start ================================== -->
<section class="contact py-80">
    <div class="container container-lg">
        <div class="row gy-5">
            <!-- Left Column: Inquiry Form -->
            <div class="col-lg-8" data-aos="fade-right" data-aos-duration="600">
                <div class="contact-box border border-neutral-100 rounded-24 p-32 p-sm-48 bg-white shadow-sm">
                    <span class="badge bg-main-50 text-main-600 text-sm py-8 px-16 rounded-pill fw-bold mb-16 d-inline-block">
                        💬 We're Here to Help
                    </span>
                    <h3 class="text-heading fw-bold mb-12">Send Us A Message</h3>
                    <p class="text-neutral-500 text-sm mb-36">
                        Have questions about our fresh farm groceries, pricing, or international export orders? Fill in the form below and our team will get back to you shortly.
                    </p>

                    <form id="contactForm" action="{{ route('contact.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="row gy-4">
                            <!-- Full Name -->
                            <div class="col-sm-6">
                                <label for="name" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Enter your name" 
                                       required>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="name_error"></div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-sm-6">
                                <label for="email" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                       id="email" 
                                       name="email" 
                                       placeholder="example@gmail.com" 
                                       required>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="email_error"></div>
                            </div>

                            <!-- Country / Region -->
                            <div class="col-sm-6">
                                <label for="contact_country" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Country / Region <span class="text-danger">*</span>
                                </label>
                                <select class="form-select bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                        id="contact_country" 
                                        name="country" 
                                        required>
                                    <option value="india" {{ $defaultCountry === 'india' ? 'selected' : '' }}>
                                        🇮🇳 India (+91)
                                    </option>
                                    <option value="mexico" {{ $defaultCountry === 'mexico' ? 'selected' : '' }}>
                                        🇲🇽 Mexico (+52)
                                    </option>
                                </select>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="country_error"></div>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-sm-6">
                                <label for="phone" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Phone Number <span class="text-danger">*</span>
                                    <span class="text-neutral-500 fw-normal text-xs ms-1" id="phone_hint">
                                        {{ $defaultCountry === 'mexico' ? '(10-digit Mexican number: 2-9)' : '(10-digit Indian number: 6,7,8,9)' }}
                                    </span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border border-neutral-100 rounded-start-12 px-14 text-sm fw-bold text-main-600" id="phone_dial_code" style="border-right: 0;">
                                        {{ $defaultCountry === 'mexico' ? '+52' : '+91' }}
                                    </span>
                                    <input type="tel" 
                                           class="form-control bg-neutral-30 border border-neutral-100 rounded-end-12 py-14 px-18 text-sm text-heading shadow-none" 
                                           id="phone" 
                                           name="phone" 
                                           placeholder="{{ $defaultCountry === 'mexico' ? '55 1234 5678' : '98765 43210' }}" 
                                           maxlength="16" 
                                           required>
                                </div>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="phone_error"></div>
                            </div>

                            <!-- Subject / Topic -->
                            <div class="col-12">
                                <label for="subject" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Inquiry Subject <span class="text-danger">*</span>
                                </label>
                                <select class="form-select bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                        id="subject" 
                                        name="subject" 
                                        required>
                                    <option value="" selected disabled>Select inquiry topic...</option>
                                    <option value="General Inquiry">General Product Inquiry</option>
                                    <option value="Order Tracking">Order &amp; Delivery Status</option>
                                    <option value="Global Export">Global / Bulk Export Query</option>
                                    <option value="Feedback">Feedback or Suggestion</option>
                                </select>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="subject_error"></div>
                            </div>

                            <!-- Message -->
                            <div class="col-12">
                                <label for="message" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Your Message <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                          id="message" 
                                          name="message" 
                                          rows="5" 
                                          placeholder="Tell us what you need or how we can assist you..." 
                                          required></textarea>
                                <div class="invalid-feedback text-danger text-xs mt-2" id="message_error"></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-32">
                                <button type="submit" id="contactSubmitBtn" class="btn btn-main py-16 px-40 rounded-pill d-inline-flex align-items-center gap-10 fw-bold shadow-sm">
                                    <span id="contactSubmitText">Send Message</span>
                                    <i class="ph-bold ph-paper-plane-tilt text-lg" id="contactSubmitIcon"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Contact Details & Store Info -->
            <div class="col-lg-4" data-aos="fade-left" data-aos-duration="600">
                <div class="contact-box border border-neutral-100 rounded-24 p-32 p-sm-40 bg-neutral-30 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="badge bg-white text-main-600 text-xs py-6 px-14 rounded-pill fw-bold mb-16 d-inline-block border border-neutral-100">
                            📍 Pal Varnan Headquarters
                        </span>
                        <h4 class="text-heading fw-bold mb-24">Get In Touch</h4>

                        <div class="d-flex flex-column gap-24 mb-36">
                            <!-- Phone -->
                            <div class="d-flex align-items-start gap-16">
                                <span class="w-44 h-44 flex-center rounded-circle bg-main-50 text-main-600 text-xl flex-shrink-0 mt-2">
                                    <i class="ph-fill ph-phone-call"></i>
                                </span>
                                <div>
                                    <span class="text-xs text-neutral-500 fw-semibold text-uppercase d-block mb-4">Phone Support</span>
                                    <a href="tel:+918778509639" class="text-heading fw-bold text-base hover-text-main-600 text-decoration-none transition-1 d-block">
                                        +91 8778509639
                                    </a>
                                    <small class="text-neutral-500 text-xs">Mon – Sat, 8:00 AM – 8:00 PM</small>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="d-flex align-items-start gap-16">
                                <span class="w-44 h-44 flex-center rounded-circle bg-main-50 text-main-600 text-xl flex-shrink-0 mt-2">
                                    <i class="ph-fill ph-envelope"></i>
                                </span>
                                <div>
                                    <span class="text-xs text-neutral-500 fw-semibold text-uppercase d-block mb-4">Email Addresses</span>
                                    <a href="mailto:apnstores369@gmail.com" class="text-heading fw-medium text-sm hover-text-main-600 text-decoration-none transition-1 d-block mb-2">
                                        apnstores369@gmail.com
                                    </a>
                                    <a href="mailto:apaulskitchen@gmail.com" class="text-heading fw-medium text-sm hover-text-main-600 text-decoration-none transition-1 d-block">
                                        apaulskitchen@gmail.com
                                    </a>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="d-flex align-items-start gap-16">
                                <span class="w-44 h-44 flex-center rounded-circle bg-main-50 text-main-600 text-xl flex-shrink-0 mt-2">
                                    <i class="ph-fill ph-map-pin"></i>
                                </span>
                                <div>
                                    <span class="text-xs text-neutral-500 fw-semibold text-uppercase d-block mb-4">Our Store &amp; Office</span>
                                    <p class="text-heading fw-medium text-sm mb-0 line-height-1-6">
                                        Nachiarkudiyuruppu, Opp. Govt. Bus Stand,<br>
                                        Kanyakumari - 629702,<br>
                                        Tamil Nadu, India.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Quick Connect Buttons -->
                    <div class="d-flex flex-column gap-12 mt-20">
                        <a href="tel:+918778509639" class="btn btn-main py-12 px-20 rounded-12 d-flex align-items-center justify-content-between text-decoration-none shadow-none">
                            <span class="d-flex align-items-center gap-10">
                                <i class="ph-bold ph-phone-outgoing text-lg"></i>
                                <span>Call Helpline Now</span>
                            </span>
                            <i class="ph-bold ph-arrow-right"></i>
                        </a>

                        <a href="https://maps.google.com/?q=Kanyakumari+Government+Bus+Stand" target="_blank" rel="noopener noreferrer" class="btn btn-outline-neutral-100 bg-white py-12 px-20 rounded-12 d-flex align-items-center justify-content-between text-heading hover-border-main-600 hover-text-main-600 text-decoration-none transition-1">
                            <span class="d-flex align-items-center gap-10">
                                <i class="ph-bold ph-navigation-arrow text-main-600 text-lg"></i>
                                <span>Get Directions on Map</span>
                            </span>
                            <i class="ph-bold ph-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ============================ Contact Section End ================================== -->

<!-- ============================ Google Map Section Start ================================== -->
<section class="map-section pb-80">
    <div class="container container-lg">
        <div class="rounded-24 overflow-hidden border border-neutral-100 shadow-sm position-relative" style="height: 380px;">
            <iframe 
                src="https://maps.google.com/maps?q=Kanyakumari+Government+Bus+Stand&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Pal Varnan Location Map">
            </iframe>
        </div>
    </div>
</section>
<!-- ============================ Google Map Section End ================================== -->

<!-- ========================= Trust Shipping Badges Start ========================= -->
<section class="shipping py-40 mb-40">
    <div class="container container-lg">
        <div class="row g-4">
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="300">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-car-profile"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Reliable Delivery</h6>
                        <span class="text-neutral-500 text-xs">Safe and timely doorstep shipping</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="500">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-hand-coins"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Best Value Pricing</h6>
                        <span class="text-neutral-500 text-xs">Direct farm savings for you</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="700">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-credit-card"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">100% Secure Payment</h6>
                        <span class="text-neutral-500 text-xs">Encrypted transactions guaranteed</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="900">
                <div class="shipping-item d-flex align-items-center gap-16 p-24 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-28 flex-shrink-0">
                        <i class="ph-fill ph-chats"></i>
                    </span>
                    <div>
                        <h6 class="mb-4 text-heading fw-bold text-sm">Friendly Support</h6>
                        <span class="text-neutral-500 text-xs">Dedicated helpline +91 8778509639</span>
                    </div>
                </div>
        </div>
    </div>
</section>
<!-- ========================= Trust Shipping Badges End ========================= -->

<style>
.contact-box .is-invalid {
    border-color: #dc3545 !important;
    background-color: #fff8f8 !important;
}
.contact-box .is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
}
.contact-box .invalid-feedback {
    display: none;
    font-size: 0.8125rem;
    color: #dc3545;
    font-weight: 500;
}
.contact-box .is-invalid ~ .invalid-feedback,
.contact-box .input-group:has(.is-invalid) ~ .invalid-feedback {
    display: block;
}
#phone_dial_code {
    user-select: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    const countrySelect = document.getElementById('contact_country');
    const dialBadge = document.getElementById('phone_dial_code');
    const phoneHint = document.getElementById('phone_hint');
    const phoneInput = document.getElementById('phone');

    const fields = {
        name: {
            el: document.getElementById('name'),
            errorEl: document.getElementById('name_error'),
            validate: function (val) {
                val = (val || '').trim();
                if (!val) return 'Full Name is required.';
                if (val.length < 2) return 'Full Name must be at least 2 characters.';
                if (!/^[a-zA-Z\s\.\'\-]+$/.test(val)) return 'Full Name can only contain letters, spaces, and hyphens.';
                if (val.length > 100) return 'Full Name cannot exceed 100 characters.';
                return '';
            }
        },
        email: {
            el: document.getElementById('email'),
            errorEl: document.getElementById('email_error'),
            validate: function (val) {
                val = (val || '').trim();
                if (!val) return 'Email Address is required.';
                const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(val)) return 'Please enter a valid email address (e.g. name@domain.com).';
                if (val.length > 255) return 'Email cannot exceed 255 characters.';
                return '';
            }
        },
        country: {
            el: countrySelect,
            errorEl: document.getElementById('country_error'),
            validate: function (val) {
                val = (val || '').trim().toLowerCase();
                if (!val || (val !== 'india' && val !== 'mexico')) {
                    return 'Please select a valid country.';
                }
                return '';
            }
        },
        phone: {
            el: phoneInput,
            errorEl: document.getElementById('phone_error'),
            validate: function (val) {
                val = (val || '').trim();
                if (!val) return 'Phone Number is required.';

                const selectedCountry = countrySelect ? (countrySelect.value || 'india').toLowerCase() : 'india';
                const digits = val.replace(/[^0-9]/g, '');

                if (digits.length === 0) {
                    return 'Please enter a valid phone number containing digits.';
                }

                if (selectedCountry === 'india') {
                    // Normalize Indian number: strip leading 91 (if 12 digits) or 0 (if 11 digits)
                    let national = digits;
                    if (digits.length === 12 && digits.startsWith('91')) {
                        national = digits.substring(2);
                    } else if (digits.length === 11 && digits.startsWith('0')) {
                        national = digits.substring(1);
                    }

                    if (national.length < 10) {
                        return `Indian mobile number must be 10 digits (currently ${national.length} digits).`;
                    }
                    if (national.length > 10) {
                        return `Indian mobile number cannot exceed 10 digits (entered ${national.length} digits).`;
                    }
                    if (!/^[6-9]/.test(national)) {
                        return 'Indian mobile number must start with 6, 7, 8, or 9 (e.g. 98765 43210).';
                    }
                    if (/^([0-9])\1{9}$/.test(national)) {
                        return 'Please enter a valid mobile number, not repeated identical digits.';
                    }
                    return '';
                } else if (selectedCountry === 'mexico') {
                    // Normalize Mexican number: strip leading 521 (if 13 digits) or 52 (if 12 digits)
                    let national = digits;
                    if (digits.length === 13 && digits.startsWith('521')) {
                        national = digits.substring(3);
                    } else if (digits.length === 12 && digits.startsWith('52')) {
                        national = digits.substring(2);
                    }

                    if (national.length < 10) {
                        return `Mexican phone number must be 10 digits (currently ${national.length} digits).`;
                    }
                    if (national.length > 10) {
                        return `Mexican phone number cannot exceed 10 digits (entered ${national.length} digits).`;
                    }
                    if (!/^[2-9]/.test(national)) {
                        return 'Mexican phone number must start with a valid area code (digits 2-9, e.g. 55 1234 5678).';
                    }
                    if (/^([0-9])\1{9}$/.test(national)) {
                        return 'Please enter a valid phone number, not repeated identical digits.';
                    }
                    return '';
                }

                return '';
            }
        },
        subject: {
            el: document.getElementById('subject'),
            errorEl: document.getElementById('subject_error'),
            validate: function (val) {
                val = (val || '').trim();
                if (!val) return 'Please select an inquiry topic.';
                return '';
            }
        },
        message: {
            el: document.getElementById('message'),
            errorEl: document.getElementById('message_error'),
            validate: function (val) {
                val = (val || '').trim();
                if (!val) return 'Your Message is required.';
                if (val.length < 10) return 'Message must be at least 10 characters long.';
                if (val.length > 3000) return 'Message cannot exceed 3000 characters.';
                return '';
            }
        }
    };

    function validateField(fieldName) {
        const field = fields[fieldName];
        if (!field || !field.el) return true;

        const error = field.validate(field.el.value);
        if (error) {
            field.el.classList.add('is-invalid');
            if (field.errorEl) {
                field.errorEl.textContent = error;
                field.errorEl.style.display = 'block';
            }
            return false;
        } else {
            field.el.classList.remove('is-invalid');
            if (field.errorEl) {
                field.errorEl.textContent = '';
                field.errorEl.style.display = 'none';
            }
            return true;
        }
    }

    // Dynamic country UI switcher
    function updateCountryUI(country) {
        country = (country || 'india').toLowerCase();
        if (country === 'mexico') {
            if (dialBadge) dialBadge.textContent = '+52';
            if (phoneHint) phoneHint.textContent = '(10-digit Mexican number: 2-9)';
            if (phoneInput) phoneInput.placeholder = '55 1234 5678';
        } else {
            if (dialBadge) dialBadge.textContent = '+91';
            if (phoneHint) phoneHint.textContent = '(10-digit Indian number: 6,7,8,9)';
            if (phoneInput) phoneInput.placeholder = '98765 43210';
        }

        // Re-validate phone field immediately if user already typed something
        if (phoneInput && phoneInput.value.trim().length > 0) {
            validateField('phone');
        }
    }

    if (countrySelect) {
        countrySelect.addEventListener('change', function () {
            updateCountryUI(this.value);
            validateField('country');
        });
        // Run once on load to guarantee sync
        updateCountryUI(countrySelect.value);
    }

    // Input restrictions on Phone field
    if (phoneInput) {
        phoneInput.setAttribute('inputmode', 'tel');

        phoneInput.addEventListener('keydown', function (e) {
            // Allow control and navigation keys
            if (
                e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' ||
                e.key === 'Escape' || e.key === 'Enter' ||
                e.key === 'ArrowLeft' || e.key === 'ArrowRight' ||
                e.key === 'ArrowUp' || e.key === 'ArrowDown' ||
                e.key === 'Home' || e.key === 'End' ||
                e.ctrlKey || e.metaKey || e.altKey
            ) {
                return;
            }

            // Allow '+' ONLY as the first character
            if (e.key === '+' && this.selectionStart === 0 && !this.value.includes('+')) {
                return;
            }

            // Allow spaces and hyphens for friendly formatting
            if (e.key === ' ' || e.key === '-') {
                return;
            }

            // Block any non-digit key from being typed
            if (e.key.length === 1 && !/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        });

        phoneInput.addEventListener('beforeinput', function (e) {
            if (e.data && !/^[0-9+\s\-]+$/.test(e.data)) {
                e.preventDefault();
            }
        });

        phoneInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text') || '';
            const clean = text.replace(/[^0-9+\s\-]/g, '');
            document.execCommand('insertText', false, clean.substring(0, 16));
        });
    }

    // Block numeric digits on Full Name field
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
                if (/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
            }
        });

        nameInput.addEventListener('beforeinput', function (e) {
            if (e.data && /[0-9]/.test(e.data)) {
                e.preventDefault();
            }
        });
    }

    // Real-time validation on input, change, and blur
    Object.keys(fields).forEach(key => {
        const field = fields[key];
        if (!field.el) return;

        field.el.addEventListener('input', function () {
            // Live sanitization for phone
            if (key === 'phone') {
                const hasPlus = this.value.startsWith('+');
                this.value = (hasPlus ? '+' : '') + this.value.replace(/[^0-9\s\-]/g, '');
            }
            if (this.classList.contains('is-invalid')) {
                validateField(key);
            }
        });

        field.el.addEventListener('blur', function () {
            validateField(key);
        });

        if (field.el.tagName === 'SELECT') {
            field.el.addEventListener('change', function () {
                validateField(key);
            });
        }
    });

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validate all fields
        let isValid = true;
        let firstInvalid = null;

        Object.keys(fields).forEach(key => {
            const ok = validateField(key);
            if (!ok && isValid) {
                isValid = false;
                firstInvalid = fields[key].el;
            }
        });

        if (!isValid) {
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        const submitBtn = document.getElementById('contactSubmitBtn');
        const submitText = document.getElementById('contactSubmitText');
        const submitIcon = document.getElementById('contactSubmitIcon');
        const formData = new FormData(contactForm);

        // Visual loading state & lock
        submitBtn.disabled = true;
        submitText.textContent = 'Sending Message...';
        submitIcon.className = 'ph-bold ph-spinner ph-spin text-lg';

        fetch("{{ route('contact.store') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                // If server returned field validation errors, display them on specific inputs
                if (data.errors) {
                    Object.keys(data.errors).forEach(errKey => {
                        if (fields[errKey] && fields[errKey].el) {
                            fields[errKey].el.classList.add('is-invalid');
                            if (fields[errKey].errorEl) {
                                fields[errKey].errorEl.textContent = data.errors[errKey][0];
                            }
                        }
                    });
                }
                let errorMsg = data.message || 'Something went wrong. Please check your inputs.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(errorMsg);
            }
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Message Sent!',
                html: data.message || 'Thank you for reaching out to Pal Varnan. We will contact you shortly.',
                confirmButtonColor: window.palMainColor || '#1c799b'
            });

            contactForm.reset();
            Object.keys(fields).forEach(key => {
                if (fields[key].el) fields[key].el.classList.remove('is-invalid');
                if (fields[key].errorEl) fields[key].errorEl.textContent = '';
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: error.message || 'Unable to send message right now. Please try again.',
                confirmButtonColor: '#e02b2b'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.textContent = 'Send Message';
            submitIcon.className = 'ph-bold ph-paper-plane-tilt text-lg';
        });
    });
});
</script>

@endsection
