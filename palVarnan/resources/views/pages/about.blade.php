@extends('layouts.app')

@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">About Us</h6>
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
                <li class="text-sm text-main-600 fw-semibold"> About Us </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- ========================= About Story Hero Start ========================= -->
<section class="about-hero py-80">
    <div class="container container-lg">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="600">
                <div class="about-content pe-xl-40">
                    <span class="badge bg-main-50 text-main-600 text-sm py-8 px-16 rounded-pill fw-bold mb-16 d-inline-block">
                        🌾 Welcome to Pal Varnan
                    </span>
                    <h2 class="text-heading fw-bold mb-24 line-height-1-3">
                        Pure Farm-Fresh Groceries &amp; Trusted Global Exports
                    </h2>
                    <p class="text-neutral-600 text-base line-height-1-8 mb-20">
                        At <strong class="text-heading">Pal Varnan</strong>, we believe that healthy living starts with genuine, chemical-free food. Based in Kanyakumari, Tamil Nadu, we source authentic groceries, traditional spices, fresh batters, cold-pressed oils, and farm produce directly from dedicated growers and local artisans.
                    </p>
                    <p class="text-neutral-600 text-base line-height-1-8 mb-32">
                        Beyond supplying local households with everyday pantry essentials, our global export division delivers certified Indian groceries, traditional delicacies, gifts, and toys to international communities—ensuring the timeless taste of home is never out of reach.
                    </p>

                    <div class="d-flex align-items-center gap-24 flex-wrap">
                        <a href="{{ url('/products') }}" class="btn btn-main d-inline-flex align-items-center rounded-pill py-14 px-32 gap-8 text-decoration-none">
                            <span>Explore Shop</span>
                            <i class="ph-bold ph-arrow-right"></i>
                        </a>
                        <div class="d-flex align-items-center gap-12">
                            <span class="w-48 h-48 flex-center rounded-circle bg-main-50 text-main-600 text-xl flex-shrink-0">
                                <i class="ph-bold ph-phone-call"></i>
                            </span>
                            <div class="d-flex flex-column">
                                <span class="text-xs text-neutral-500 fw-medium">Have Questions? Call Us</span>
                                <a href="tel:+918778509639" class="text-heading fw-bold text-sm hover-text-main-600 text-decoration-none">+91 8778509639</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="600">
                <div class="about-thumb position-relative text-center">
                    <div class="rounded-24 overflow-hidden p-24 bg-neutral-30 border border-neutral-100 d-inline-block">
                        <img src="/assets/images/thumbs/basket-img.png" alt="Farm Fresh Basket - Pal Varnan" class="img-fluid rounded-16" style="max-height: 440px; object-fit: contain;">
                    </div>
                    <div class="position-absolute bottom-0 start-0 mb-n16 ms-20 bg-white p-20 rounded-16 shadow-lg border border-neutral-100 text-start d-none d-sm-block">
                        <div class="d-flex align-items-center gap-12">
                            <span class="w-44 h-44 flex-center rounded-circle bg-success-600 text-white text-xl">
                                <i class="ph-fill ph-check-circle"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 text-heading fw-bold text-sm">100% Quality Assured</h6>
                                <span class="text-xs text-neutral-500">Naturally grown &amp; hygienically packed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= About Story Hero End ========================= -->

<!-- ========================= Core Values Start ========================= -->
<section class="core-values py-80 bg-neutral-30">
    <div class="container container-lg">
        <div class="section-heading text-center max-w-600 mx-auto mb-48" data-aos="fade-up">
            <span class="text-main-600 fw-bold text-sm text-uppercase letter-spacing-1 d-block mb-8">Our Standards</span>
            <h3 class="text-heading fw-bold mb-12">Why Choose Pal Varnan?</h3>
            <p class="text-neutral-500 text-sm">We are dedicated to uncompromised food purity, transparent sourcing, and prompt customer care.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Feature 1 -->
            <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-duration="300">
                <div class="p-32 rounded-16 bg-white border border-neutral-100 h-100 text-center transition-2 hover-shadow">
                    <span class="w-64 h-64 flex-center rounded-circle bg-main-50 text-main-600 text-32 mx-auto mb-20">
                        <i class="ph-bold ph-plant"></i>
                    </span>
                    <h5 class="text-heading fw-bold text-lg mb-12">Direct From Farm</h5>
                    <p class="text-neutral-600 text-sm mb-0 line-height-1-6">
                        Fresh produce and ingredients sourced directly from verified regional growers, cutting out middlemen for unbeatable freshness.
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-duration="500">
                <div class="p-32 rounded-16 bg-white border border-neutral-100 h-100 text-center transition-2 hover-shadow">
                    <span class="w-64 h-64 flex-center rounded-circle bg-main-50 text-main-600 text-32 mx-auto mb-20">
                        <i class="ph-bold ph-shield-check"></i>
                    </span>
                    <h5 class="text-heading fw-bold text-lg mb-12">Hygienic Packaging</h5>
                    <p class="text-neutral-600 text-sm mb-0 line-height-1-6">
                        Clean processing and food-grade packaging preserve natural nutrients, authentic aromas, and long-lasting freshness.
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-duration="700">
                <div class="p-32 rounded-16 bg-white border border-neutral-100 h-100 text-center transition-2 hover-shadow">
                    <span class="w-64 h-64 flex-center rounded-circle bg-main-50 text-main-600 text-32 mx-auto mb-20">
                        <i class="ph-bold ph-truck"></i>
                    </span>
                    <h5 class="text-heading fw-bold text-lg mb-12">Express Delivery</h5>
                    <p class="text-neutral-600 text-sm mb-0 line-height-1-6">
                        Prompt order dispatch and trusted shipping partners bring kitchen essentials directly to your doorstep on schedule.
                    </p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-duration="900">
                <div class="p-32 rounded-16 bg-white border border-neutral-100 h-100 text-center transition-2 hover-shadow">
                    <span class="w-64 h-64 flex-center rounded-circle bg-main-50 text-main-600 text-32 mx-auto mb-20">
                        <i class="ph-bold ph-globe-hemisphere-east"></i>
                    </span>
                    <h5 class="text-heading fw-bold text-lg mb-12">Global Export Network</h5>
                    <p class="text-neutral-600 text-sm mb-0 line-height-1-6">
                        Serving overseas markets with certified exports of traditional staples, regional snacks, gifts, and seasonal specialties.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Core Values End ========================= -->

<!-- ========================= Milestone Counter Start ========================= -->
<section class="counter py-80">
    <div class="container container-lg">
        <div class="bg-main-600 rounded-24 p-48 text-white" data-aos="zoom-in" data-aos-duration="600">
            <div class="row g-4 text-center">
                <div class="col-lg-3 col-6">
                    <div class="p-16">
                        <h2 class="text-white fw-bold display-five mb-8">15K+</h2>
                        <p class="text-white text-opacity-75 mb-0 fw-medium text-base">Happy Households</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="p-16">
                        <h2 class="text-white fw-bold display-five mb-8">500+</h2>
                        <p class="text-white text-opacity-75 mb-0 fw-medium text-base">Pantry Products</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="p-16">
                        <h2 class="text-white fw-bold display-five mb-8">100%</h2>
                        <p class="text-white text-opacity-75 mb-0 fw-medium text-base">Natural &amp; Pure</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="p-16">
                        <h2 class="text-white fw-bold display-five mb-8">Global</h2>
                        <p class="text-white text-opacity-75 mb-0 fw-medium text-base">Shipping Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Milestone Counter End ========================= -->

<!-- ========================= Location & Mission Section Start ========================= -->
<section class="mission py-60">
    <div class="container container-lg">
        <div class="p-40 rounded-24 bg-neutral-30 border border-neutral-100">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-7" data-aos="fade-right">
                    <span class="text-main-600 fw-bold text-sm text-uppercase letter-spacing-1 d-block mb-8">Our Heritage</span>
                    <h3 class="text-heading fw-bold mb-16">Rooted in Kanyakumari, Serving the World</h3>
                    <p class="text-neutral-600 text-sm line-height-1-8 mb-20">
                        Our main operations and packing unit are located at <strong>Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari - 629702</strong>. From this heritage coast, we blend ancestral culinary traditions with modern supply chain standards so you always receive genuine products at honest prices.
                    </p>
                    <div class="d-flex align-items-center gap-16 flex-wrap">
                        <div class="d-flex align-items-center gap-8">
                            <i class="ph-bold ph-envelope-simple text-main-600 text-lg"></i>
                            <a href="mailto:apnstores369@gmail.com" class="text-heading text-sm fw-medium text-decoration-none hover-text-main-600">apnstores369@gmail.com</a>
                        </div>
                        <span class="text-neutral-300">|</span>
                        <div class="d-flex align-items-center gap-8">
                            <i class="ph-bold ph-envelope-simple text-main-600 text-lg"></i>
                            <a href="mailto:apaulskitchen@gmail.com" class="text-heading text-sm fw-medium text-decoration-none hover-text-main-600">apaulskitchen@gmail.com</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-lg-end text-center" data-aos="fade-left">
                    <a href="{{ url('/products') }}" class="btn btn-main rounded-pill py-16 px-40 text-base fw-bold text-decoration-none">
                        Browse Products Catalogue
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Location & Mission Section End ========================= -->

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
