@extends('layouts.app')

@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">Shipping &amp; Delivery Policy</h6>
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
                <li class="text-sm text-main-600 fw-semibold"> Shipping &amp; Delivery Policy </li>
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
                            <a href="{{ route('terms.conditions') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-transparent text-neutral-600 hover-text-main-600 hover-bg-white fw-medium text-sm text-decoration-none transition-1">
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
                            <a href="{{ route('shipping.policy') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-white border border-main-200 text-main-600 fw-bold text-sm text-decoration-none shadow-none">
                                <i class="ph-bold ph-truck text-base"></i>
                                <span>Shipping &amp; Delivery</span>
                            </a>
                        </li>
                    </ul>

                    <div class="p-20 rounded-16 bg-white border border-neutral-100">
                        <h6 class="text-heading fw-bold mb-8 text-sm">Need Delivery Help?</h6>
                        <p class="text-neutral-500 text-xs mb-16">Have questions regarding shipping times, courier partners, or tracking your parcel?</p>
                        <a href="mailto:apnstores369@gmail.com" class="text-main-600 text-xs fw-bold d-inline-flex align-items-center gap-6 text-decoration-none">
                            <i class="ph-bold ph-envelope-simple"></i>
                            <span>apnstores369@gmail.com</span>
                        </a>
                        <div class="mt-8">
                            <a href="tel:+918778509639" class="text-neutral-700 hover-text-main-600 text-xs fw-semibold d-inline-flex align-items-center gap-6 text-decoration-none">
                                <i class="ph-bold ph-phone-call"></i>
                                <span>+91 8778509639</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy Text Main Card -->
            <div class="col-lg-8">
                <div class="policy-card bg-white border border-neutral-100 rounded-24 p-32 p-sm-48 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-12 mb-28 pb-20 border-bottom border-neutral-40">
                        <div>
                            <span class="text-neutral-400 text-xs fw-semibold text-uppercase letter-spacing-1">Pal Varnan e-Commerce</span>
                            <h2 class="text-heading fw-bold text-2xl mb-0">Shipping &amp; Delivery Policy</h2>
                        </div>
                        <span class="badge bg-main-50 text-main-600 text-xs py-6 px-12 rounded-pill fw-semibold">
                            Last Updated: October 2026
                        </span>
                    </div>

                    <div class="policy-body text-neutral-600 text-sm line-height-1-8 d-flex flex-column gap-28">
                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">1. Shipping Overview &amp; Coverage</h5>
                            <p class="mb-0">
                                <strong>Pal Varnan</strong> provides reliable and prompt delivery services across all serviceable pin codes in India, alongside dedicated international export shipping to select overseas destinations including Mexico and Latin America. We partner with India's top logistics aggregators including <strong>Shiprocket</strong>, certified express courier providers (Delhivery, Blue Dart, DTDC, India Post), and international freight carriers to ensure your groceries arrive safe and fresh.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">2. Order Processing &amp; Dispatch Schedule</h5>
                            <p class="mb-8">We take pride in rapid turnaround times for everyday kitchen essentials and export goods:</p>
                            <ul class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li><strong>Dispatch Window:</strong> Domestic orders are packaged and handed over to logistics partners within <strong>24 to 48 hours</strong> of order confirmation (Monday through Saturday).</li>
                                <li><strong>Cutoff Times:</strong> Orders placed before 1:00 PM IST on working business days are queued for priority same-day packaging.</li>
                                <li><strong>Sunday &amp; Public Holidays:</strong> Orders placed on Sundays or official holidays are packed and dispatched on the subsequent business day.</li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">3. Estimated Delivery Timelines (Domestic India)</h5>
                            <p class="mb-8">Transit times depend on your destination geographic zone from our Kanyakumari fulfillment base:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered border-neutral-100 rounded-12 overflow-hidden text-sm mb-0">
                                    <thead class="bg-neutral-30 text-heading fw-bold">
                                        <tr>
                                            <th class="py-12 px-16">Delivery Region</th>
                                            <th class="py-12 px-16">Estimated Transit Time</th>
                                            <th class="py-12 px-16">Courier Service</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="py-12 px-16 fw-semibold text-heading">Tamil Nadu &amp; Kerala</td>
                                            <td class="py-12 px-16">1 – 2 Business Days</td>
                                            <td class="py-12 px-16">Express Road / Surface</td>
                                        </tr>
                                        <tr>
                                            <td class="py-12 px-16 fw-semibold text-heading">South India Metros (Bengaluru, Hyderabad, Chennai)</td>
                                            <td class="py-12 px-16">2 – 3 Business Days</td>
                                            <td class="py-12 px-16">Priority Express Air / Surface</td>
                                        </tr>
                                        <tr>
                                            <td class="py-12 px-16 fw-semibold text-heading">North, West &amp; Central India</td>
                                            <td class="py-12 px-16">3 – 5 Business Days</td>
                                            <td class="py-12 px-16">National Express Cargo</td>
                                        </tr>
                                        <tr>
                                            <td class="py-12 px-16 fw-semibold text-heading">Northeast &amp; Remote Island Pincodes</td>
                                            <td class="py-12 px-16">5 – 7 Business Days</td>
                                            <td class="py-12 px-16">India Post Speed Post / Air Cargo</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">4. Shipping Charges &amp; Calculation</h5>
                            <p class="mb-0">
                                Shipping fees are calculated dynamically at checkout based on the aggregate dead weight or volumetric weight of your cart, combined with your destination pin code via our Shiprocket live API. Any ongoing promotional free shipping thresholds will be automatically applied at the checkout review stage.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">5. International Shipping &amp; Exports (Mexico &amp; Global)</h5>
                            <p class="mb-8">
                                For our international clientele purchasing traditional grocery items, dry staples, gifts, and export commodities:
                            </p>
                            <ul class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li><strong>Delivery Timeframe:</strong> International shipments typically take <strong>7 to 15 business days</strong> depending on air route schedules and local customs clearance.</li>
                                <li><strong>Customs, Import Duties &amp; Taxes:</strong> All international shipments are subject to the customs inspections and tax regulations of the destination country (e.g. Mexico SAT). Any import duties, tariffs, or clearance charges imposed by local authorities are the responsibility of the recipient unless specified as Delivered Duty Paid (DDP).</li>
                                <li><strong>Food Safety &amp; Phytosanitary Regulations:</strong> Pal Varnan packages export items in compliance with standard international food-grade preservation and phytosanitary packaging standards.</li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">6. Shipment Tracking &amp; Delivery Updates</h5>
                            <p class="mb-0">
                                Immediately upon carrier dispatch, an automated notification containing your courier tracking number (AWB) and direct tracking URL will be sent to your registered email and mobile number. You can also view real-time delivery milestones anytime directly within your <a href="{{ route('account.index') }}" class="text-main-600 fw-semibold">My Account</a> order details panel.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">7. Incomplete Addresses &amp; Failed Deliveries</h5>
                            <p class="mb-0">
                                To prevent delivery failures or rerouting delays, please double-check your shipping address, landmark, and postal code during checkout. Our delivery courier will attempt delivery up to 3 times before returning the shipment to our warehouse. In the event of non-delivery due to incorrect address information provided by the customer, reshipment charges may apply.
                            </p>
                        </div>

                        <div class="p-24 rounded-16 bg-neutral-30 border border-neutral-100">
                            <h5 class="text-heading fw-bold text-base mb-8">8. Shipping Support Desk</h5>
                            <p class="mb-8">For inquiries regarding consignment status, tracking delays, or bulk export shipments, contact our logistics desk:</p>
                            <div class="d-flex flex-column gap-4 text-xs">
                                <span><strong>Pal Varnan / Paul's Kitchen Logistics Center</strong></span>
                                <span>Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari - 629702, Tamil Nadu, India.</span>
                                <span>Email: <a href="mailto:apnstores369@gmail.com" class="text-main-600 fw-medium">apnstores369@gmail.com</a> | Helpline: <a href="tel:+918778509639" class="text-main-600 fw-medium">+91 8778509639</a></span>
                                <span>Desk Hours: Monday – Saturday: 9:00 AM – 8:00 PM IST</span>
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
