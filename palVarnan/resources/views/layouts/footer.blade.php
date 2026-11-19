<!-- ==================== Footer Start Here ==================== -->
<footer class="footer pt-56 pt-lg-80 pb-32 bg-white">
    <div class="container container-lg">
        <div class="row g-4 gy-4 gy-md-5 align-items-start">
            <!-- 1. Brand & Contact Details -->
            <div class="col-12 col-xl-3" data-aos="fade-up" data-aos-duration="200">
                <div class="footer-item pe-xl-16">
                    <div class="footer-item__logo mb-16">
                        <a href="/"> 
                            <img src="/assets/images/logo/logo.png" alt="Pal Varnan" style="max-height: 50px; width: auto; object-fit: contain;">
                        </a>
                    </div>
                    <p class="text-neutral-600 text-14 mb-20 line-height-1-6">
                        Pal Varnan is your trusted grocery store delivering farm-fresh essentials, savouries, gifts, and daily needs.
                    </p>

                    <div class="d-flex flex-column gap-12">
                        <div class="d-flex align-items-start gap-12 text-heading text-14">
                            <span class="footer-contact-icon flex-center rounded-circle bg-main-50 text-main-600 flex-shrink-0">
                                <i class="ph-bold ph-map-pin"></i>
                            </span>
                            <span class="line-height-1-5 pt-2">Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari-629702.</span>
                        </div>
                        <a href="mailto:apnstores369@gmail.com" class="d-flex align-items-center gap-12 text-heading text-14 hover-text-main-600 transition-1">
                            <span class="footer-contact-icon flex-center rounded-circle bg-main-50 text-main-600 flex-shrink-0">
                                <i class="ph-bold ph-envelope"></i>
                            </span>
                            <span>apnstores369@gmail.com</span>
                        </a>
                        <a href="mailto:apaulskitchen@gmail.com" class="d-flex align-items-center gap-12 text-heading text-14 hover-text-main-600 transition-1">
                            <span class="footer-contact-icon flex-center rounded-circle bg-main-50 text-main-600 flex-shrink-0">
                                <i class="ph-bold ph-envelope"></i>
                            </span>
                            <span>apaulskitchen@gmail.com</span>
                        </a>
                        <a href="tel:+918778509639" class="d-flex align-items-center gap-12 text-heading text-14 hover-text-main-600 transition-1">
                            <span class="footer-contact-icon flex-center rounded-circle bg-main-50 text-main-600 flex-shrink-0">
                                <i class="ph-bold ph-phone-call"></i>
                            </span>
                            <span class="fw-semibold">+91 8778509639</span>
                        </a>
                    </div>
                    <div class="w-100 d-xl-none border-bottom border-gray-100 mt-28 mb-12"></div>
                </div>
            </div>

            <!-- 2. Quick Links -->
            <div class="col-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="400">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-20 text-heading">Quick Links</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="{{ route('about') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>About Us</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ url('/products') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Shop Products</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('categories.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>All Categories</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('contact') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Contact Us</span>
                            </a>
                        </li>
                        <li class="mb-0">
                            <a href="{{ route('account.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Track Order</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 3. Customer Care & Policies -->
            <div class="col-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="600">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-20 text-heading">Customer Care</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="{{ route('privacy.policy') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Privacy Policy</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('terms.conditions') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Terms & Conditions</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('shipping.policy') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Shipping Policy</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('refund.policy') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Return & Refund</span>
                            </a>
                        </li>
                        <li class="mb-0">
                            <a href="{{ route('contact') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Help & Support</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4. My Account -->
            <div class="col-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="800">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-20 text-heading">My Account</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="{{ route('account.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('account.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Order History</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ url('/cart') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Shopping Cart</span>
                            </a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('wishlist.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Wishlist</span>
                            </a>
                        </li>
                        <li class="mb-0">
                            <a href="{{ route('checkout.index') }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 d-inline-flex align-items-center gap-6">
                                <span>Checkout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 5. Categories -->
            <div class="col-6 col-md-3 col-xl-3" data-aos="fade-up" data-aos-duration="1000">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-20 text-heading">Categories</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        @php
                            $footerCategories = \App\Models\Category::whereNotIn('category_name', ['wefbbhf'])
                                ->take(5)
                                ->get();
                        @endphp
                        @foreach($footerCategories as $fCat)
                            <li class="mb-12">
                                <a href="/products?category={{ \Illuminate\Support\Str::slug($fCat->category_name) }}" class="footer-menu__link text-neutral-600 text-14 hover-text-main-600 text-capitalize d-inline-flex align-items-center gap-6">
                                    <span>{{ $fCat->category_name }}</span>
                                </a>
                            </li>
                        @endforeach
                        <li class="mt-8">
                            <a href="{{ route('categories.index') }}" class="btn-all-cat text-main-600 fw-bold text-13 hover-text-main-800 d-inline-flex align-items-center gap-6">
                                <span>View All Categories</span>
                                <i class="ph-bold ph-arrow-right text-xs"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 6. Global Exports & Payments Strip -->
            <div class="col-12 mt-32 mt-lg-48" data-aos="fade-up" data-aos-duration="1200">
                <div class="footer-global-strip p-20 p-sm-24 p-md-28 rounded-20 border">
                    <div class="row align-items-center justify-content-between gy-16">
                        <div class="col-12 col-md-7 col-lg-8">
                            <h6 class="text-16 fw-bold mb-6 text-heading d-flex align-items-center gap-10">
                                <span class="w-32 h-32 flex-center rounded-circle bg-main-100 text-main-600 flex-shrink-0 text-base">
                                    <i class="ph-bold ph-globe"></i>
                                </span>
                                <span>Global Exports & Trusted Delivery</span>
                            </h6>
                            <p class="text-sm text-neutral-600 mb-0 ps-sm-42 line-height-1-6">
                                Pal Varnan exports high-quality grocery products, traditional savouries, gifts, and toys to international markets with guaranteed freshness and timely delivery.
                            </p>
                        </div>
                        <div class="col-12 col-md-5 col-lg-4 text-md-end mt-8 mt-md-0">
                            <span class="text-xs fw-semibold text-neutral-500 d-block mb-8">Accepted Secure Payments</span>
                            <img src="/assets/images/thumbs/method.png" alt="Accepted Payment Methods" class="img-fluid" style="max-height: 30px; width: auto; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- bottom Footer -->
<div class="bottom-footer py-16 border-top border-gray-100 bg-white">
    <div class="container container-lg">
        <div class="bottom-footer__inner d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start gap-12 gap-md-16">
            <p class="bottom-footer__text text-neutral-600 text-13 fw-medium mb-0">
                Copyright &copy; 2026 <a href="/" class="text-main-600 fw-bold hover-text-main-800 text-decoration-none">Pal Varnan</a>. All Rights Reserved.
            </p>
            <div class="flex-align gap-8 flex-wrap">
               <p class="bottom-footer__text text-neutral-500 text-13 fw-medium mb-0">
                   Designed & Developed by <a href="https://saitechnosolutions.com/" target="_blank" class="text-main-600 fw-bold hover-text-main-800 text-decoration-none">Sai Techno Solutions</a>
               </p>
            </div>
        </div>
    </div>
</div>

<style>
/* Footer Custom Styles - Clean Typography, Perfect Alignment & Padding */
.footer-contact-icon {
    width: 32px;
    height: 32px;
    font-size: 15px;
    transition: all 0.25s ease;
}
.footer .footer-item a:hover .footer-contact-icon {
    background-color: var(--main-600, #026966) !important;
    color: #ffffff !important;
}

.footer-item__title {
    position: relative;
    padding-bottom: 8px;
    letter-spacing: -0.2px;
}
.footer-item__title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 26px;
    height: 2.5px;
    background-color: var(--main-600, #026966);
    border-radius: 4px;
}

.footer-menu__link {
    transition: transform 0.2s ease, color 0.2s ease;
}
.footer-menu__link:hover {
    color: var(--main-600, #026966) !important;
    transform: translateX(4px);
}

.footer-global-strip {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #e2e8f0 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

@media (max-width: 767px) {
    .footer {
        padding-top: 40px !important;
        padding-bottom: 24px !important;
    }
    .footer-item {
        margin-bottom: 8px;
    }
    .footer-item__title {
        font-size: 15px !important;
        margin-bottom: 14px !important;
    }
    .footer-menu li {
        margin-bottom: 10px !important;
    }
    .footer-menu__link {
        font-size: 13.5px !important;
    }
    .footer-global-strip {
        padding: 16px 14px !important;
    }
    .bottom-footer {
        padding-top: 16px !important;
        padding-bottom: 24px !important;
    }
    .bottom-footer__inner {
        padding-right: 44px !important;
    }
}
</style>
<!-- ==================== Footer End Here ==================== -->
