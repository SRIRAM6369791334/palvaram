@extends('layouts.app')

@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0 text-heading fw-bold">Return &amp; Refund Policy</h6>
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
                <li class="text-sm text-main-600 fw-semibold"> Return &amp; Refund Policy </li>
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
                            <a href="{{ route('refund.policy') }}" class="d-flex align-items-center gap-10 py-10 px-14 rounded-10 bg-white border border-main-200 text-main-600 fw-bold text-sm text-decoration-none shadow-none">
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
                        <h6 class="text-heading fw-bold mb-8 text-sm">Need Help with Returns?</h6>
                        <p class="text-neutral-500 text-xs mb-16">Facing an issue with your received package? Get in touch with our support team immediately.</p>
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
                            <h2 class="text-heading fw-bold text-2xl mb-0">Return &amp; Refund Policy</h2>
                        </div>
                        <span class="badge bg-main-50 text-main-600 text-xs py-6 px-12 rounded-pill fw-semibold">
                            Last Updated: October 2026
                        </span>
                    </div>

                    <div class="policy-body text-neutral-600 text-sm line-height-1-8 d-flex flex-column gap-28">
                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">1. Overview &amp; Customer Satisfaction</h5>
                            <p class="mb-0">
                                At <strong>Pal Varnan</strong> (Paul's Kitchen), customer satisfaction and food safety are our highest priorities. We take extreme care in sourcing, inspecting, packing, and dispatching premium groceries, organic produce, farm-fresh spices, traditional delicacies, and export items. However, if you are not fully satisfied with your purchase due to damage, defect, or wrong items, we are here to help with a fair and clear return and refund resolution.
                            </p>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">2. Eligibility &amp; Return Windows</h5>
                            <p class="mb-8">Given the delicate nature of food and perishable groceries, different return windows apply depending on the product type:</p>
                            <ul class="d-flex flex-column gap-8 ps-20 mb-0">
                                <li>
                                    <strong>Perishable Groceries (Fresh Produce, Dairy, Bakery):</strong> Must be reported within <strong>24 to 48 hours</strong> of delivery if received in a spoiled, damaged, or unusable state.
                                </li>
                                <li>
                                    <strong>Packaged &amp; Non-Perishable Groceries (Spices, Grains, Oils, Dry Fruits, Pickles):</strong> Return or replacement request must be submitted within <strong>7 days</strong> from the delivery date. The item must be unused, in its original tamper-proof packaging with security seals intact.
                                </li>
                                <li>
                                    <strong>Non-Returnable Items:</strong> Items opened or consumed beyond initial quality check, custom gift bundles, clearance flash sale items, or products damaged due to customer mishandling after delivery cannot be accepted for return.
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">3. Damaged, Defective, or Incorrect Deliveries</h5>
                            <p class="mb-8">
                                If you receive a package that is visibly damaged during transit, leaked, expired, or contains items different from what you ordered:
                            </p>
                            <ul class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li>Take clear photographs or a short video showing the outer shipping label, package condition, and the affected item.</li>
                                <li>Contact our customer support immediately via email (<a href="mailto:apnstores369@gmail.com" class="text-main-600 fw-semibold">apnstores369@gmail.com</a>) or phone (<a href="tel:+918778509639" class="text-main-600 fw-semibold">+91 8778509639</a>) along with your Order ID.</li>
                                <li>Upon verification, we will promptly arrange a free replacement dispatch or provide a 100% refund without hassle.</li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">4. Return Process &amp; Pickup</h5>
                            <p class="mb-8">
                                Initiating a return is straightforward:
                            </p>
                            <ol class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li><strong>Submit Request:</strong> Reach out to support with your order number and issue description.</li>
                                <li><strong>Approval &amp; Pickup:</strong> Once verified, our logistics team (Shiprocket or local courier) will schedule a doorstep reverse pickup for serviceable pin codes across India.</li>
                                <li><strong>Self-Shipping Alternative:</strong> If reverse pickup is unavailable in your remote pin code, our support team may advise self-shipping (with reimbursement of standard courier fees) or issue an instant refund based on photographic proof.</li>
                            </ol>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">5. Refund Processing &amp; Timelines</h5>
                            <p class="mb-8">
                                Once an approved return is received and inspected at our fulfillment hub (or upon digital verification for perishable claims):
                            </p>
                            <ul class="d-flex flex-column gap-6 ps-20 mb-0">
                                <li><strong>Online Prepaid Payments (UPI, Cards, Net Banking):</strong> Refunds will be credited directly to the original bank account or card within <strong>5 to 7 working days</strong>, subject to your bank's clearance cycles.</li>
                                <li><strong>International Orders (Mexico / Global):</strong> Refunds for export orders processed through international payment gateways or PayPal will reflect within <strong>7 to 10 working days</strong> in the original currency.</li>
                                <li><strong>Cash on Delivery (COD) Orders:</strong> Refunds will be transferred directly to your verified bank account via NEFT / IMPS or UPI ID provided during the claim process.</li>
                            </ul>
                        </div>

                        <div>
                            <h5 class="text-heading fw-bold text-base mb-10">6. Order Cancellation Policy</h5>
                            <p class="mb-0">
                                You can cancel an order free of charge before it has been dispatched from our store. Once an order is processed, packed, and handed over to our shipping carriers, it cannot be intercepted in transit; standard return procedures will apply upon receipt.
                            </p>
                        </div>

                        <div class="p-24 rounded-16 bg-neutral-30 border border-neutral-100">
                            <h5 class="text-heading fw-bold text-base mb-8">7. Grievance &amp; Escalation Contact</h5>
                            <p class="mb-8">If you have any questions or require special assistance regarding a return or refund claim, please contact our escalation desk:</p>
                            <div class="d-flex flex-column gap-4 text-xs">
                                <span><strong>Pal Varnan / Paul's Kitchen</strong></span>
                                <span>Nachiarkudiyuruppu, Opp. Govt. Bus Stand, Kanyakumari - 629702, Tamil Nadu, India.</span>
                                <span>Email: <a href="mailto:apnstores369@gmail.com" class="text-main-600 fw-medium">apnstores369@gmail.com</a> | Helpline: <a href="tel:+918778509639" class="text-main-600 fw-medium">+91 8778509639</a></span>
                                <span>Operating Hours: Monday – Saturday: 9:00 AM – 8:00 PM IST</span>
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
