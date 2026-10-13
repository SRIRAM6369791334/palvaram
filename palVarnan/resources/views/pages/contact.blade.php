@extends('layouts.app')

@section('main')

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

                    <form action="#" method="POST" onsubmit="event.preventDefault(); Swal.fire({ icon: 'success', title: 'Message Sent!', text: 'Thank you for reaching out to Pal Varnan. We will contact you shortly.', confirmButtonColor: palMainColor }); this.reset();">
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
                            </div>

                            <!-- Phone Number -->
                            <div class="col-sm-6">
                                <label for="phone" class="form-label text-sm fw-semibold text-heading mb-8">
                                    Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control bg-neutral-30 border border-neutral-100 rounded-12 py-14 px-18 text-sm text-heading shadow-none" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="+91 98765 43210" 
                                       required>
                            </div>

                            <!-- Subject / Topic -->
                            <div class="col-sm-6">
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
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-32">
                                <button type="submit" class="btn btn-main py-16 px-40 rounded-pill d-inline-flex align-items-center gap-10 fw-bold shadow-sm">
                                    <span>Send Message</span>
                                    <i class="ph-bold ph-paper-plane-tilt text-lg"></i>
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
    </div>
</section>
<!-- ========================= Trust Shipping Badges End ========================= -->

@endsection
