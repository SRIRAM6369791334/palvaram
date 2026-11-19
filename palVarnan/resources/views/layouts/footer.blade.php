<!-- ==================== Footer Start Here ==================== -->
<footer class="footer py-60 py-lg-90 bg-white">
    <div class="container container-lg">
        <div class="row gy-36 gy-lg-48 gx-24 gx-md-32 gx-xl-48 align-items-start">
            <!-- 1. Brand & Contact Details -->
            <div class="col-12 col-xl-3" data-aos="fade-up" data-aos-duration="200">
                <div class="footer-item pe-xl-16">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-5 col-xl-12 mb-16 mb-md-0 mb-xl-20">
                            <div class="footer-item__logo mb-16">
                                <a href="/"> 
                                    <img src="/assets/images/logo/logo.png" alt="Pal Varnan" style="max-height: 52px; width: auto; object-fit: contain;">
                                </a>
                            </div>
                            <p class="mb-0 text-heading text-15">We're Grocery Shop, an innovative team of food suppliers.</p>
                        </div>
                        <div class="col-12 col-md-7 col-xl-12">
                            <div class="d-flex flex-column gap-10">
                                <p class="text-heading fw-medium text-14 mb-0 d-flex align-items-start gap-8">
                                    <i class="ph-bold ph-map-pin text-main-600 mt-2 flex-shrink-0 text-base"></i>
                                    <span>Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari-629702.</span>
                                </p>
                                <a href="mailto:apnstores369@gmail.com" class="text-heading fw-medium text-14 hover-text-main-600 d-inline-flex align-items-center gap-8">
                                    <i class="ph-bold ph-envelope text-main-600 flex-shrink-0 text-base"></i>
                                    <span>apnstores369@gmail.com</span>
                                </a>
                                <a href="mailto:apaulskitchen@gmail.com" class="text-heading fw-medium text-14 hover-text-main-600 d-inline-flex align-items-center gap-8">
                                    <i class="ph-bold ph-envelope text-main-600 flex-shrink-0 text-base"></i>
                                    <span>apaulskitchen@gmail.com</span>
                                </a>
                                <a href="tel:+918778509639" class="text-heading fw-medium text-14 hover-text-main-600 d-inline-flex align-items-center gap-8">
                                    <i class="ph-bold ph-phone-call text-main-600 flex-shrink-0 text-base"></i>
                                    <span>+91 8778509639</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-xl-none border-bottom border-neutral-100 mt-24 mb-12"></div>
                </div>
            </div>

            <!-- 2. Information -->
            <div class="col-6 col-sm-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="400">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-18">Information</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Become a Vendor</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Affiliate Program</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('privacy.policy') }}" class="text-heading text-14 hover-text-main-600">Privacy Policy</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Our Suppliers</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('shipping.policy') }}" class="text-heading text-14 hover-text-main-600">Shipping Policy</a>
                        </li>
                        <li class="">
                            <a href="{{ route('refund.policy') }}" class="text-heading text-14 hover-text-main-600">Return & Refund Policy</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 3. Customer Support -->
            <div class="col-6 col-sm-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="600">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-18">Customer Support</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="{{ route('contact') }}" class="text-heading text-14 hover-text-main-600">Help Center</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('contact') }}" class="text-heading text-14 hover-text-main-600">Contact Us</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('terms.conditions') }}" class="text-heading text-14 hover-text-main-600">Terms & Conditions</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('terms.conditions') }}" class="text-heading text-14 hover-text-main-600">Policies & Rules</a>
                        </li>
                        <li class="">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Online Shopping</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4. My Account -->
            <div class="col-6 col-sm-6 col-md-3 col-xl-2" data-aos="fade-up" data-aos-duration="800">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-18">My Account</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="{{ route('account.index') }}" class="text-heading text-14 hover-text-main-600">My Account</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('account.index') }}" class="text-heading text-14 hover-text-main-600">Order History</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ url('/cart') }}" class="text-heading text-14 hover-text-main-600">Shopping Cart</a>
                        </li>
                        <li class="mb-12">
                            <a href="{{ route('wishlist.index') }}" class="text-heading text-14 hover-text-main-600">Wishlist</a>
                        </li>
                        <li class="">
                            <a href="{{ route('about') }}" class="text-heading text-14 hover-text-main-600">About Us</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 5. Daily Groceries -->
            <div class="col-6 col-sm-6 col-md-3 col-xl-3" data-aos="fade-up" data-aos-duration="1000">
                <div class="footer-item">
                    <h6 class="footer-item__title text-16 fw-bold mb-18">Daily Groceries</h6>
                    <ul class="footer-menu list-unstyled p-0 m-0">
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Dairy & Eggs</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Meat & Seafood</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Breakfast Food</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Household Supplies</a>
                        </li>
                        <li class="mb-12">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Bread & Bakery</a>
                        </li>
                        <li class="">
                            <a href="/products" class="text-heading text-14 hover-text-main-600">Pantry Staples</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 6. Global Exports & Payments -->
            <div class="col-12 mt-28 mt-lg-40" data-aos="fade-up" data-aos-duration="1200">
                <div class="footer-global-strip p-24 p-md-28 rounded-20 bg-neutral-30 border border-neutral-100">
                    <div class="row align-items-center justify-content-between gy-16">
                        <div class="col-12 col-md-7 col-lg-8">
                            <h6 class="text-base fw-bold mb-6 text-heading d-flex align-items-center gap-8">
                                <i class="ph-bold ph-globe text-main-600 text-lg"></i>
                                <span>Global Exports</span>
                            </h6>
                            <p class="text-sm text-neutral-600 mb-0">Pal Varnan exports high-quality grocery products, gifts, and toys to international markets with a strong focus on reliability and timely delivery.</p>
                        </div>
                        <div class="col-12 col-md-5 col-lg-4 text-md-end mt-8 mt-md-0">
                            <span class="text-xs fw-semibold text-neutral-500 d-block mb-8">Accepted Secure Payments</span>
                            <img src="/assets/images/thumbs/method.png" alt="Accepted Payment Methods" class="img-fluid" style="max-height: 32px; width: auto; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- bottom Footer -->
<div class="bottom-footer py-16">
    <div class="container container-lg">
        <div class="bottom-footer__inner d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start gap-12 gap-md-16 py-16 border-top border-neutral-50 pe-md-0 pe-60 pb-56 pb-md-0">
            <p class="bottom-footer__text text-heading wow fadeInLeft fw-medium mb-0">Copyright &copy; 2026 <span class="text-success-600 fw-semibold"><a href="/"> Pal Varnan</a></span> All Rights Reserved </p>
            <div class="flex-align gap-8 flex-wrap wow fadeInRight">
               <p class="bottom-footer__text text-heading wow fadeInLeft fw-medium mb-0">Developed By <span class="text-success-600 fw-semibold"><a href="https://saitechnosolutions.com/" target="_blank">Sai Techno Solutions</a></span> </p>
            </div>
        </div>
    </div>
</div>
<!-- ==================== Footer End Here ==================== -->
