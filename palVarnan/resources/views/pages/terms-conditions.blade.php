@extends('layouts.app')

@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">Terms &amp; Conditions</h6>
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
                <li class="text-sm text-main-600 fw-semibold"> Terms &amp; Conditions </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- ========================= Policy Content Section Start ========================= -->
<section class="policy-section py-80">
    <div class="container container-lg">
        <div class="row gy-5">
            <!-- Sidebar Quick Navigation & Support -->
            <div class="col-lg-4">
                <div class="policy-sidebar border border-neutral-100 rounded-24 p-28 bg-neutral-30 sticky-top" style="top: 100px;">
                    <span class="badge bg-main-50 text-main-600 text-xs py-6 px-12 rounded-pill fw-bold mb-16 d-inline-block">
                        📜 Legal Documents
                    </span>
                    <h5 class="text-heading fw-bold mb-20">Quick Navigation</h5>
                    <ul class="d-flex flex-column gap-12 list-unstyled p-0 mb-32">
                        <li>
                            <a href="{{ route('privacy.policy') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-transparent text-neutral-600 hover-text-main-600 hover-bg-white fw-medium text-sm text-decoration-none transition-1">
                                <i class="ph-bold ph-shield-check text-base"></i>
                                <span>Privacy Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms.conditions') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-white border border-main-200 text-main-600 fw-bold text-sm text-decoration-none shadow-none">
                                <i class="ph-bold ph-file-text text-base"></i>
                                <span>Terms &amp; Conditions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('refund.policy') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-transparent text-neutral-600 hover-text-main-600 hover-bg-white fw-medium text-sm text-decoration-none transition-1">
                                <i class="ph-bold ph-arrows-clockwise text-base"></i>
                                <span>Return &amp; Refund Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shipping.policy') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-transparent text-neutral-600 hover-text-main-600 hover-bg-white fw-medium text-sm text-decoration-none transition-1">
                                <i class="ph-bold ph-truck text-base"></i>
                                <span>Shipping &amp; Delivery</span>
                            </a>
                        </li>
                    </ul>

                    <div class="p-20 rounded-16 bg-white border border-neutral-100">
                        <h6 class="text-heading fw-bold mb-8 text-sm">Need Help?</h6>
                        <p class="text-neutral-500 text-xs mb-16">Contact our support team for any clarifications on our store terms.</p>
                        <a href="mailto:apnstores369@gmail.com" class="text-main-600 text-xs fw-bold d-inline-flex align-items-center gap-6 text-decoration-none">
                            <i class="ph-bold ph-envelope-simple"></i>
                            <span>apnstores369@gmail.com</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Policy Text Main Card -->
            <div class="col-lg-8">
                <div class="policy-card bg-white border border-neutral-100 rounded-24 p-32 p-sm-48 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-12 mb-28 pb-20 border-bottom border-neutral-40">
                        <div>
                            <span class="text-neutral-400 text-xs fw-semibold text-uppercase letter-spacing-1">Pal Varnan e-Commerce</span>
                            <h2 class="text-heading fw-bold text-2xl mb-0">Terms &amp; Conditions</h2>
                        </div>
                        <span class="badge bg-main-50 text-main-600 text-xs py-6 px-12 rounded-pill fw-semibold">
                            Last Updated: October 2026
                        </span>
                    </div>

                    <div class="policy-body text-neutral-600 text-sm line-height-1-8 d-flex flex-column gap-28">
                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">1. Acceptance of Terms</h5>
                            <p class="mb-0">
                                By accessing, browsing, or purchasing products on <strong>Pal Varnan</strong>, you acknowledge that you have read, understood, and agreed to be bound by these Terms and Conditions. If you do not agree to any part of these terms, please refrain from using our online store.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">2. Account Registration &amp; Security</h5>
                            <p class="mb-0">
                                When creating an account on Pal Varnan, you agree to provide true, accurate, and current information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. Notify us immediately of any unauthorized account activity.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">3. Product Descriptions &amp; Pricing</h5>
                            <p class="mb-8">
                                We make every effort to display accurate product images, nutritional details, ingredients, and prices. However:
                            </p>
                            <ul class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li>Prices and stock availability are subject to change without prior notice.</li>
                                <li>Because agricultural produce and farm-fresh items are subject to seasonal variations, packaging and color shades may vary slightly from online representations.</li>
                                <li>In the event of an inadvertent pricing typographical error, we reserve the right to cancel or amend the affected order before fulfillment with full refund.</li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">4. Orders, Payments &amp; Invoicing</h5>
                            <p class="mb-8">
                                An order confirmation email represents an acknowledgment of your request and does not constitute a final binding contract until products are dispatched. We accept payment through supported debit/credit cards, UPI, net banking, and certified payment gateways.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">5. International Orders &amp; Global Exports</h5>
                            <p class="mb-0">
                                For customers ordering from international jurisdictions (e.g. Mexico or other export destinations), cross-border shipments may be subject to import duties, local taxes, or customs clearance requirements of the destination country, which are the buyer's responsibility unless explicitly stated otherwise.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">6. Intellectual Property</h5>
                            <p class="mb-0">
                                All logos, trademarks, graphics, product photography, text content, and software on this website are the intellectual property of Pal Varnan or its content suppliers and are protected under Indian and international copyright laws.
                            </p>
                        </div>

                        <div class="p-24 rounded-16 bg-neutral-30 border border-neutral-100">
                            <h5 class="text-heading fw-bold text-base mb-8">7. Governing Law &amp; Jurisdiction</h5>
                            <p class="mb-8">These terms shall be governed by and construed in accordance with the laws of India. Any disputes arising in connection with these terms shall be subject to the exclusive jurisdiction of the courts in Kanyakumari / Tamil Nadu.</p>
                            <div class="d-flex flex-column gap-4 text-xs">
                                <span><strong>Pal Varnan / Paul's Kitchen</strong></span>
                                <span>Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari - 629702, Tamil Nadu, India.</span>
                                <span>Email: <a href="mailto:apnstores369@gmail.com" class="text-main-600 fw-medium">apnstores369@gmail.com</a> | Phone: <a href="tel:+918778509639" class="text-main-600 fw-medium">+91 8778509639</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Policy Content Section End ========================= -->

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
