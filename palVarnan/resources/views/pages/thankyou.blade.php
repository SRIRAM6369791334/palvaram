@extends('layouts.app')

@section('main')
<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Order Confirmation</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        <i class="ph ph-house"></i> Home
                    </a>
                </li>
                <li class="flex-align"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600">Order Success</li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<section class="py-80">
    <div class="container container-lg">
        <div class="bg-white border border-gray-100 hover-border-main-600 transition-1 rounded-24 p-32 p-sm-48 max-w-700 mx-auto text-center" style="box-shadow: 0 10px 40px rgba(0,0,0,0.04);">
            
            <!-- Success Icon -->
            <div class="w-88 h-88 rounded-circle bg-main-50 text-main-600 border border-main-200 flex-center mx-auto mb-24 shadow-sm" style="font-size: 48px;">
                <i class="ph-bold ph-check"></i>
            </div>
            
            <span class="badge bg-success-50 text-success-600 border border-success-200 text-xs px-16 py-6 rounded-pill fw-bold mb-16 text-uppercase" style="letter-spacing: 0.5px;">
                Payment Confirmed
            </span>

            <h3 class="mb-12 fw-bold text-gray-900 font-heading-two text-28">Order Placed Successfully!</h3>
            <p class="text-gray-600 text-sm mb-32 max-w-500 mx-auto">
                Thank you for your purchase from Pal Varnan. We have received your order and our team is preparing it with care.
            </p>

            <!-- Order Timeline / Steps -->
            <div class="order-timeline bg-gray-50 border border-gray-100 rounded-16 p-20 mb-32">
                <div class="row g-2 text-center">
                    <div class="col-3">
                        <div class="timeline-step active">
                            <span class="step-icon w-36 h-36 rounded-circle bg-main-600 text-white flex-center mx-auto mb-6 text-sm fw-bold">
                                <i class="ph-bold ph-check"></i>
                            </span>
                            <span class="text-xs fw-bold text-gray-900 d-block">Confirmed</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="timeline-step">
                            <span class="step-icon w-36 h-36 rounded-circle bg-white text-main-600 border border-main-200 flex-center mx-auto mb-6 text-sm fw-bold">
                                2
                            </span>
                            <span class="text-xs text-gray-600 d-block">Processing</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="timeline-step">
                            <span class="step-icon w-36 h-36 rounded-circle bg-white text-gray-400 border border-gray-200 flex-center mx-auto mb-6 text-sm fw-bold">
                                3
                            </span>
                            <span class="text-xs text-gray-500 d-block">Shipped</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="timeline-step">
                            <span class="step-icon w-36 h-36 rounded-circle bg-white text-gray-400 border border-gray-200 flex-center mx-auto mb-6 text-sm fw-bold">
                                4
                            </span>
                            <span class="text-xs text-gray-500 d-block">Delivered</span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(isset($order))
                <div class="bg-gray-50 border border-gray-100 rounded-16 p-24 mb-32 text-start">
                    <div class="flex-between align-items-center mb-16 pb-12 border-bottom border-gray-200 flex-wrap gap-8">
                        <div>
                            <span class="text-gray-500 text-xs d-block">Order Reference Number</span>
                            <strong class="text-gray-900 font-heading-two text-base">#{{ $order->order_number }}</strong>
                        </div>
                        <span class="badge bg-main-50 text-main-600 border border-main-200 px-12 py-6 rounded-pill text-xs fw-semibold">
                            {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'Just now' }}
                        </span>
                    </div>

                    <div class="row g-12 text-sm mb-16">
                        <div class="col-sm-6">
                            <div class="flex-between">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="badge bg-gray-200 text-gray-800 px-10 py-4 rounded-pill fw-medium">{{ $order->payment_method }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="flex-between">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="badge bg-success-50 text-success-600 border border-success-200 px-10 py-4 rounded-pill fw-semibold">{{ $order->payment_status }}</span>
                            </div>
                        </div>
                        @if($order->courier_name)
                        <div class="col-sm-6">
                            <div class="flex-between">
                                <span class="text-gray-600">Courier Partner:</span>
                                <span class="text-gray-900 fw-semibold">{{ $order->courier_name }}</span>
                            </div>
                        </div>
                        @endif
                        @if($order->awb_code)
                        <div class="col-sm-6">
                            <div class="flex-between">
                                <span class="text-gray-600">AWB Tracking Code:</span>
                                <strong class="text-main-600">{{ $order->awb_code }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="flex-between pt-16 border-top border-gray-200 align-items-center">
                        <span class="text-gray-900 fw-bold text-base">Total Amount Paid</span>
                        <strong class="text-main-600 text-20 fw-bold font-heading-two">₹{{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                </div>
            @endif

            <p class="text-xs text-gray-500 mb-32 flex-center gap-6">
                <i class="ph ph-envelope-simple text-sm text-main-600"></i>
                <span>Order receipt and updates have been dispatched to your email.</span>
            </p>

            <div class="d-flex justify-content-center gap-16 flex-wrap">
                <a href="/products" class="btn btn-main py-14 px-36 rounded-8 fw-semibold flex-align gap-8">
                    <span>Continue Shopping</span>
                    <i class="ph-bold ph-arrow-right"></i>
                </a>
                <a href="/myaccount" class="btn btn-outline-main py-14 px-32 rounded-8 fw-semibold flex-align gap-8">
                    <i class="ph-bold ph-user"></i>
                    <span>View in My Account</span>
                </a>
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
