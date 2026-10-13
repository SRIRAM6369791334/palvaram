@extends('layouts.app')
@section('main')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">My Account</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        <i class="ph ph-house"></i> Home
                    </a>
                </li>
                <li class="flex-align"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600">My Account</li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<!-- ================================ Account Section Start ================================ -->
<section class="account-page py-80">
    <div class="container container-lg">
        <div class="row gy-4">

            <!-- ===== Sidebar Start ===== -->
            <div class="col-xl-3 col-lg-4">
                <div class="account-sidebar border border-gray-100 hover-border-main-600 transition-1 rounded-16 px-24 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">

                    <!-- User Info Card -->
                    <div class="text-center mb-28 pb-28 border-bottom border-gray-100">
                        <div class="w-72 h-72 rounded-circle bg-main-50 text-main-600 border border-main-200 flex-center mx-auto mb-16 shadow-sm" style="font-size: 28px; font-weight: 700;">
                            @if(Auth::check() && Auth::user()->name)
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @else
                                <i class="ph ph-user"></i>
                            @endif
                        </div>
                        <h6 class="text-lg fw-bold mb-4 text-gray-900">
                            {{ Auth::user()->name ?? 'Valued Customer' }}
                        </h6>
                        <span class="text-xs text-gray-500 d-block mb-8">
                            {{ Auth::user()->email ?? '' }}
                        </span>
                        <span class="badge bg-main-50 text-main-600 border border-main-200 rounded-pill px-12 py-4 text-xs fw-semibold">
                            Member since {{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : '2026' }}
                        </span>
                    </div>

                    <!-- Nav Menu -->
                    <ul class="account-nav d-flex flex-column gap-6 p-0 mb-0 list-unstyled">
                        <li>
                            <a href="#" class="account-nav__link active-tab flex-align gap-12 px-16 py-12 rounded-8 fw-medium"
                               data-tab="orders" onclick="switchTab(event,'orders')">
                                <i class="ph ph-package text-xl flex-shrink-0"></i>
                                <span>My Orders</span>
                                @if($orders->count() > 0)
                                    <span class="ms-auto bg-gray-100 text-gray-700 text-xs rounded-pill px-8 py-2 fw-semibold">
                                        {{ $orders->count() }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 fw-medium"
                               data-tab="personal" onclick="switchTab(event,'personal')">
                                <i class="ph ph-user-circle text-xl flex-shrink-0"></i>
                                <span>Personal Details</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 fw-medium"
                               data-tab="addresses" onclick="switchTab(event,'addresses')">
                                <i class="ph ph-map-pin text-xl flex-shrink-0"></i>
                                <span>My Addresses</span>
                                <span class="ms-auto bg-gray-100 text-gray-700 text-xs rounded-pill px-8 py-2 fw-semibold">
                                    {{ $addresses->count() }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 fw-medium"
                               data-tab="wishlist" onclick="switchTab(event,'wishlist')">
                                <i class="ph ph-heart text-xl flex-shrink-0"></i>
                                <span>Wishlist</span>
                                <span class="ms-auto bg-main-50 text-main-600 border border-main-200 text-xs rounded-pill px-8 py-2 fw-semibold">
                                    {{ $wishlistItems->count() }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 fw-medium"
                               data-tab="cart" onclick="switchTab(event,'cart')">
                                <i class="ph ph-shopping-cart text-xl flex-shrink-0"></i>
                                <span>My Cart</span>
                                <span class="ms-auto bg-main-50 text-main-600 border border-main-200 text-xs rounded-pill px-8 py-2 fw-semibold">
                                    {{ $cartItems->count() }}
                                </span>
                            </a>
                        </li>
                        <li class="mt-16 pt-16 border-top border-gray-100">
                            <form action="{{ route('logout') }}" method="POST" id="accLogoutForm" class="m-0">
                                @csrf
                                <button type="submit" class="w-100 text-start border-0 bg-transparent flex-align gap-12 px-16 py-12 rounded-8 text-danger-600 hover-text-danger-700 hover-bg-danger-50 transition-1 fw-medium" style="color: #dc2626;">
                                    <i class="ph ph-sign-out text-xl flex-shrink-0"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- ===== Sidebar End ===== -->

            <!-- ===== Main Content Start ===== -->
            <div class="col-xl-9 col-lg-8">

                <!-- ========== Orders Tab ========== -->
                <div id="tab-orders" class="account-tab">
                    <div class="border border-gray-100 rounded-16 px-32 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-28 pb-16 border-bottom border-gray-100 flex-wrap gap-12">
                            <div>
                                <h6 class="text-xl mb-4 font-heading-two fw-bold">My Orders</h6>
                                <p class="text-xs text-gray-500 mb-0">Track and review all your previous grocery and product purchases</p>
                            </div>
                            <span class="badge bg-main-50 text-main-600 border border-main-200 rounded-pill px-12 py-6 text-xs fw-semibold">
                                Total Orders: {{ $orders->count() }}
                            </span>
                        </div>

                        @if($orders->count() > 0)
                            <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                                <table class="table style-three cart-table mb-0">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Order ID</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Product Name</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Date</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Price</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center">Qty</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Total</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            @foreach ($order->items as $item)
                                                <tr class="border-bottom border-gray-100">
                                                    <td class="py-16 px-16">
                                                        <span class="text-main-600 fw-bold text-sm">#{{ $order->order_number }}</span>
                                                        @if($order->awb_code)
                                                            <div class="text-xs text-gray-500 mt-4 flex-align gap-4">
                                                                <i class="ph ph-truck text-main-600"></i>
                                                                <span>{{ $order->courier_name ?? 'Shiprocket' }} ({{ $order->awb_code }})</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="py-16 px-16">
                                                        <div class="d-flex align-items-center gap-12">
                                                            <div class="border border-gray-100 rounded-8 p-4 bg-white flex-shrink-0" style="width:48px;height:48px;">
                                                                <img src="{{ $item->product_image ? asset('storage/' . $item->product_image) : asset('assets/images/thumbs/product-img1.png') }}" 
                                                                     alt="{{ $item->product_name }}" 
                                                                     class="w-100 h-100 object-fit-contain"
                                                                     onerror="this.src='/assets/images/logo/logo.png'">
                                                            </div>
                                                            <div>
                                                                <span class="text-gray-900 fw-semibold text-sm d-block">{{ $item->product_name }}</span>
                                                                @if($item->variant_name)
                                                                    <span class="text-xs text-gray-500">{{ $item->variant_name }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-16 px-16 text-nowrap">
                                                        <span class="text-gray-700 text-sm">{{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}</span>
                                                    </td>
                                                    <td class="py-16 px-16 text-nowrap">
                                                        <span class="fw-semibold text-sm">₹{{ number_format($item->price, 2) }}</span>
                                                    </td>
                                                    <td class="py-16 px-16 text-center text-nowrap">
                                                        <span class="text-gray-700 text-sm fw-medium">{{ $item->quantity }}</span>
                                                    </td>
                                                    <td class="py-16 px-16 text-nowrap">
                                                        <span class="fw-bold text-sm text-gray-900">₹{{ number_format($item->total, 2) }}</span>
                                                    </td>
                                                    <td class="py-16 px-16 text-center text-nowrap">
                                                        @php
                                                            $status = $order->status ?? 'Order Placed';
                                                            $badgeClass = 'bg-warning-50 text-warning-600 border-warning-200';
                                                            if (in_array(strtolower($status), ['delivered', 'completed'])) {
                                                                $badgeClass = 'bg-success-50 text-success-600 border-success-200';
                                                            } elseif (in_array(strtolower($status), ['cancelled', 'failed'])) {
                                                                $badgeClass = 'bg-danger-50 text-danger-600 border-danger-200';
                                                            } elseif (in_array(strtolower($status), ['shipped', 'in transit', 'out for delivery', 'awb assigned'])) {
                                                                $badgeClass = 'bg-info-50 text-info-600 border-info-200';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} border rounded-pill px-12 py-4 text-xs fw-semibold">
                                                            {{ $status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-60">
                                <span class="w-72 h-72 rounded-circle bg-gray-50 text-gray-400 flex-center mx-auto mb-16 text-36">
                                    <i class="ph ph-package"></i>
                                </span>
                                <h6 class="text-lg fw-bold text-gray-900 mb-8">No Orders Found</h6>
                                <p class="text-gray-500 text-sm mb-24 max-w-400 mx-auto">You have not placed any orders yet. Discover our pure farm groceries and authentic products today.</p>
                                <a href="/products" class="btn btn-main py-12 px-32 rounded-8 text-sm fw-semibold">
                                    <span>Start Shopping</span>
                                    <i class="ph-bold ph-arrow-right ms-4"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- ========== Orders Tab End ========== -->

                <!-- ========== Personal Details Tab ========== -->
                <div id="tab-personal" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-16 px-32 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="mb-28 pb-16 border-bottom border-gray-100">
                            <h6 class="text-xl mb-4 font-heading-two fw-bold">Personal Details</h6>
                            <p class="text-xs text-gray-500 mb-0">Update your account name, contact details and password credentials</p>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-20 mb-32">
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">First Name <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" name="name" class="common-input rounded-8 ps-44" value="{{ old('name', Auth::user()->name) }}" required>
                                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                            <i class="ph ph-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">Last Name <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" name="last_name" class="common-input rounded-8 ps-44" value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                            <i class="ph ph-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">Email Address <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="email" name="email" class="common-input rounded-8 ps-44" value="{{ old('email', Auth::user()->email) }}" required>
                                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                            <i class="ph ph-envelope-simple"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="tel" name="phone_number" class="common-input rounded-8 ps-44" value="{{ old('phone_number', Auth::user()->phone_number) }}">
                                        <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                            <i class="ph ph-phone"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password -->
                            <div class="pt-28 border-top border-gray-100">
                                <div class="mb-20">
                                    <h6 class="text-base fw-bold mb-4 font-heading-two">Security &amp; Password</h6>
                                    <p class="text-xs text-gray-500 mb-0">Leave blank if you do not wish to change your current password</p>
                                </div>

                                <div class="row g-20">
                                    <div class="col-sm-4">
                                        <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">Current Password</label>
                                        <div class="position-relative">
                                            <input type="password" name="current_password" id="acc_current_pwd" class="common-input rounded-8 ps-44 pe-44" placeholder="Current Password">
                                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                                <i class="ph ph-lock-key"></i>
                                            </span>
                                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#acc_current_pwd"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">New Password</label>
                                        <div class="position-relative">
                                            <input type="password" name="password" id="acc_new_pwd" class="common-input rounded-8 ps-44 pe-44" placeholder="New Password">
                                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                                <i class="ph ph-lock-key"></i>
                                            </span>
                                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#acc_new_pwd"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="text-neutral-900 text-sm mb-8 fw-semibold d-block">Confirm Password</label>
                                        <div class="position-relative">
                                            <input type="password" name="password_confirmation" id="acc_confirm_pwd" class="common-input rounded-8 ps-44 pe-44" placeholder="Confirm Password">
                                            <span class="position-absolute top-50 start-0 translate-middle-y ms-16 text-gray-400 text-xl pointer-events-none">
                                                <i class="ph ph-lock-key"></i>
                                            </span>
                                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y cursor-pointer ph ph-eye-slash text-xl text-gray-400 hover-text-main-600" id="#acc_confirm_pwd"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-32 pt-20 border-top border-gray-100 flex-align gap-16">
                                <button type="submit" class="btn btn-main py-12 px-36 rounded-8 fw-semibold flex-align gap-8">
                                    <i class="ph-bold ph-floppy-disk"></i>
                                    <span>Save Profile</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- ========== Personal Details Tab End ========== -->

                <!-- ========== Addresses Tab ========== -->
                <div id="tab-addresses" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-16 px-32 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-28 pb-16 border-bottom border-gray-100 flex-wrap gap-16">
                            <div>
                                <h6 class="text-xl mb-4 font-heading-two fw-bold">Saved Delivery Addresses</h6>
                                <p class="text-xs text-gray-500 mb-0">Manage and switch your default shipping and delivery locations</p>
                            </div>
                            <button type="button" class="btn btn-main py-10 px-20 rounded-8 text-sm flex-align gap-8" onclick="toggleAccountAddAddress()">
                                <i class="ph-bold ph-plus-circle text-lg"></i> <span id="acc-add-btn-text">Add New Address</span>
                            </button>
                        </div>

                        {{-- Add Address Form (Toggled) --}}
                        <div id="account-add-address-wrap" class="p-24 rounded-12 border border-gray-200 bg-gray-50 mb-32" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center mb-20">
                                <h6 class="text-base fw-bold mb-0 text-main-600 flex-align gap-6">
                                    <i class="ph-bold ph-map-pin"></i> Add New Delivery Address
                                </h6>
                                <button type="button" class="btn-close text-xs" onclick="toggleAccountAddAddress()"></button>
                            </div>
                            <form action="{{ route('address.store') }}" method="POST">
                                @csrf
                                <div class="row g-16">
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" placeholder="First Name" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" placeholder="Last Name" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone_number" placeholder="10-digit mobile number" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Email Address</label>
                                        <input type="email" name="email" placeholder="Email Address" class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Flat / House No. / Building / Street <span class="text-danger">*</span></label>
                                        <input type="text" name="address_line_one" placeholder="House No, Street, Apartment" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Area / Locality / Sector</label>
                                        <input type="text" name="address_line_two" placeholder="Area / Locality" class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Landmark (Optional)</label>
                                        <input type="text" name="landmark" placeholder="Near Temple / School / Landmark" class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">State <span class="text-danger">*</span></label>
                                        <input type="text" name="state" placeholder="State (e.g. Tamil Nadu)" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" placeholder="City" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="text-xs text-gray-700 mb-4 fw-semibold">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" name="pincode" placeholder="6-digit Pincode" maxlength="6" required class="common-input rounded-8 border-gray-200">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-xs text-gray-700 mb-6 fw-semibold d-block">Address Type</label>
                                        <div class="d-flex gap-20">
                                            <label class="flex-align gap-6 cursor-pointer text-sm">
                                                <input type="radio" name="address_type_id" value="1" checked> <span>🏠 Home</span>
                                            </label>
                                            <label class="flex-align gap-6 cursor-pointer text-sm">
                                                <input type="radio" name="address_type_id" value="2"> <span>💼 Work</span>
                                            </label>
                                            <label class="flex-align gap-6 cursor-pointer text-sm">
                                                <input type="radio" name="address_type_id" value="3"> <span>📍 Other</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-16 d-flex gap-12">
                                        <button type="submit" class="btn btn-main py-10 px-28 rounded-8 text-sm">Save Address</button>
                                        <button type="button" class="btn btn-outline-gray py-10 px-20 rounded-8 text-sm" onclick="toggleAccountAddAddress()">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Address Cards Grid --}}
                        <div class="row g-20">
                            @forelse ($addresses as $addr)
                                <div class="col-md-6">
                                    <div class="p-24 rounded-12 border border-gray-100 hover-border-main-600 transition-1 h-100 position-relative bg-white" style="box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                                        
                                        {{-- View State --}}
                                        <div id="acc-addr-view-{{ $addr->id }}">
                                            <div class="d-flex justify-content-between align-items-start mb-12">
                                                <div class="d-flex align-items-center gap-8 flex-wrap">
                                                    <h6 class="text-base fw-bold text-gray-900 mb-0">{{ $addr->address_first_name }} {{ $addr->address_last_name }}</h6>
                                                    <span class="badge bg-main-50 text-main-600 border border-main-200 text-xs px-8 py-4 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                                        <i class="ph {{ $addr->address_type_id == 1 ? 'ph-house' : ($addr->address_type_id == 2 ? 'ph-briefcase' : 'ph-map-pin') }} me-2"></i>
                                                        {{ $addr->address_type_id == 1 ? 'Home' : ($addr->address_type_id == 2 ? 'Work' : 'Other') }}
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-8">
                                                    <button type="button" class="btn btn-sm btn-outline-main py-4 px-10 rounded-6 text-xs flex-align gap-4" onclick="toggleAccountEdit({{ $addr->id }})">
                                                        <i class="ph ph-pencil-simple"></i> Edit
                                                    </button>
                                                    <form action="{{ route('address.destroy', $addr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger py-4 px-8 rounded-6 text-xs" title="Delete Address">
                                                            <i class="ph ph-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <p class="text-sm text-gray-600 mb-16 lh-base" style="min-height: 48px;">
                                                {{ $addr->address_line_one }}{{ $addr->address_line_two ? ', ' . $addr->address_line_two : '' }}
                                                @if($addr->landmark)<br><span class="text-gray-500">Landmark: {{ $addr->landmark }}</span>@endif
                                                <br>{{ $addr->city }}, {{ $addr->state }} — <strong class="text-gray-900">{{ $addr->pincode }}</strong>
                                            </p>

                                            <div class="pt-12 border-top border-gray-100 text-xs text-gray-600 d-flex flex-column gap-6">
                                                <div class="flex-align gap-8">
                                                    <i class="ph ph-phone text-main-600 text-sm"></i>
                                                    <span>{{ $addr->address_phone_number }}</span>
                                                </div>
                                                @if($addr->email)
                                                    <div class="flex-align gap-8">
                                                        <i class="ph ph-envelope text-main-600 text-sm"></i>
                                                        <span>{{ $addr->email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Edit Form State --}}
                                        <div id="acc-addr-edit-{{ $addr->id }}" style="display:none;">
                                            <div class="d-flex justify-content-between align-items-center mb-16">
                                                <h6 class="text-sm fw-bold mb-0 text-main-600">Edit Address</h6>
                                                <button type="button" class="btn-close text-xs" onclick="toggleAccountEdit({{ $addr->id }})"></button>
                                            </div>
                                            <form action="{{ route('address.update', $addr->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row g-12">
                                                    <div class="col-sm-6">
                                                        <label class="text-xs text-gray-700 mb-2">First Name</label>
                                                        <input type="text" name="first_name" value="{{ $addr->address_first_name }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-xs text-gray-700 mb-2">Last Name</label>
                                                        <input type="text" name="last_name" value="{{ $addr->address_last_name }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-xs text-gray-700 mb-2">Phone</label>
                                                        <input type="tel" name="phone_number" value="{{ $addr->address_phone_number }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-xs text-gray-700 mb-2">Email</label>
                                                        <input type="email" name="email" value="{{ $addr->email }}" class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="text-xs text-gray-700 mb-2">Street Address</label>
                                                        <input type="text" name="address_line_one" value="{{ $addr->address_line_one }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="text-xs text-gray-700 mb-2">Area / Locality</label>
                                                        <input type="text" name="address_line_two" value="{{ $addr->address_line_two }}" class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="text-xs text-gray-700 mb-2">City</label>
                                                        <input type="text" name="city" value="{{ $addr->city }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="text-xs text-gray-700 mb-2">State</label>
                                                        <input type="text" name="state" value="{{ $addr->state }}" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="text-xs text-gray-700 mb-2">Pincode</label>
                                                        <input type="text" name="pincode" value="{{ $addr->pincode }}" maxlength="6" required class="common-input rounded-8 py-8 text-xs border-gray-200">
                                                    </div>
                                                    <div class="col-12 mt-12 d-flex gap-8">
                                                        <button type="submit" class="btn btn-main py-8 px-20 rounded-6 text-xs">Save</button>
                                                        <button type="button" class="btn btn-outline-gray py-8 px-16 rounded-6 text-xs" onclick="toggleAccountEdit({{ $addr->id }})">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-40">
                                    <span class="w-64 h-64 rounded-circle bg-gray-50 text-gray-400 flex-center mx-auto mb-16 text-32">
                                        <i class="ph ph-map-pin"></i>
                                    </span>
                                    <h6 class="text-base text-gray-800 mb-4">No Saved Addresses</h6>
                                    <p class="text-gray-500 text-xs mb-20">Add your delivery location to make checkout faster.</p>
                                    <button type="button" class="btn btn-main py-10 px-24 rounded-8 text-sm" onclick="toggleAccountAddAddress()">
                                        <i class="ph-bold ph-plus me-4"></i> Add Address
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <!-- ========== Addresses Tab End ========== -->

                <!-- ========== Wishlist Tab ========== -->
                <div id="tab-wishlist" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-16 px-32 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-28 pb-16 border-bottom border-gray-100 flex-wrap gap-12">
                            <div>
                                <h6 class="text-xl mb-4 font-heading-two fw-bold">My Wishlist</h6>
                                <p class="text-xs text-gray-500 mb-0">Saved products you love and want to purchase later</p>
                            </div>
                            <span class="badge bg-main-50 text-main-600 border border-main-200 rounded-pill px-12 py-6 text-xs fw-semibold">
                                {{ $wishlistItems->count() }} Items
                            </span>
                        </div>

                        @if($wishlistItems->count() > 0)
                            <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                                <table class="table style-three cart-table mb-0">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center" style="width: 60px;">Remove</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Product</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Variant</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Price</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wishlistItems as $item)
                                            @if(!$item->product) @continue @endif
                                            @php $wVariant = $item->variant; @endphp
                                            <tr class="border-bottom border-gray-100">
                                                <td class="py-16 px-16 text-center">
                                                    <button type="button"
                                                        class="remove-wishlist-btn text-2xl text-gray-400 hover-text-danger-600 transition-1 border-0 bg-transparent p-0"
                                                        data-id="{{ $item->id }}"
                                                        title="Remove from Wishlist">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </td>
                                                <td class="py-16 px-16">
                                                    <div class="table-product d-flex align-items-center gap-12">
                                                        <a href="/product/{{ $item->product->slug }}"
                                                            class="table-product__thumb border border-gray-100 rounded-8 p-4 flex-center flex-shrink-0 bg-white"
                                                            style="width: 56px; height: 56px;">
                                                            <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}"
                                                                alt="{{ $item->product->product_name }}" 
                                                                class="w-100 h-100 object-fit-contain"
                                                                onerror="this.src='/assets/images/logo/logo.png'">
                                                        </a>
                                                        <div class="table-product__content text-start">
                                                            <h6 class="title text-base fw-semibold mb-0">
                                                                <a href="/product/{{ $item->product->slug }}" class="link text-line-2 hover-text-main-600">
                                                                    {{ $item->product->product_name }}
                                                                </a>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-16 px-16 text-nowrap">
                                                    @if($wVariant && !empty($wVariant->value) && $wVariant->unit)
                                                        <span class="text-xs fw-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-pill px-10 py-4 d-inline-block">
                                                            {{ $wVariant->value }}{{ $wVariant->unit->short_name ?? '' }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-sm">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-16 px-16 text-nowrap">
                                                    @if($wVariant && $wVariant->mrp_price)
                                                        <span class="text-xs text-gray-400 text-decoration-line-through d-block">
                                                            ₹{{ $wVariant->mrp_price }}
                                                        </span>
                                                    @endif
                                                    <span class="text-base fw-bold text-main-600">
                                                        ₹{{ $wVariant->offer_price ?? '—' }}
                                                    </span>
                                                </td>
                                                <td class="py-16 px-16 text-center text-nowrap">
                                                    <button type="button"
                                                        class="acc-add-to-cart btn btn-main py-10 px-24 rounded-8 text-sm fw-semibold flex-align gap-6"
                                                        data-product-id="{{ $item->product->id }}"
                                                        data-variant-id="{{ $item->product_varient_id }}">
                                                        <i class="ph-bold ph-shopping-cart-simple"></i>
                                                        <span>Add to Cart</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-60">
                                <span class="w-72 h-72 rounded-circle bg-gray-50 text-gray-400 flex-center mx-auto mb-16 text-36">
                                    <i class="ph ph-heart"></i>
                                </span>
                                <h6 class="text-lg fw-bold text-gray-900 mb-8">Your Wishlist is Empty</h6>
                                <p class="text-gray-500 text-sm mb-24 max-w-400 mx-auto">Explore our fresh grocery collections and save items you want to buy later.</p>
                                <a href="/products" class="btn btn-main py-12 px-32 rounded-8 text-sm fw-semibold">
                                    <span>Browse Products</span>
                                    <i class="ph-bold ph-arrow-right ms-4"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- ========== Wishlist Tab End ========== -->

                <!-- ========== Cart Tab ========== -->
                <div id="tab-cart" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-16 px-32 py-36 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-center mb-28 pb-16 border-bottom border-gray-100 flex-wrap gap-12">
                            <div>
                                <h6 class="text-xl mb-4 font-heading-two fw-bold">My Cart</h6>
                                <p class="text-xs text-gray-500 mb-0">Review items in your active shopping basket</p>
                            </div>
                            <span class="badge bg-main-50 text-main-600 border border-main-200 rounded-pill px-12 py-6 text-xs fw-semibold">
                                {{ $cartItems->count() }} Items
                            </span>
                        </div>

                        @if($cartItems->count() > 0)
                            <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                                <table class="table style-three cart-table mb-0">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center" style="width: 60px;">Remove</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Product</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16">Price</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-center">Quantity</th>
                                            <th class="h6 mb-0 text-sm fw-bold py-16 px-16 text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                            @if(!$item->product) @continue @endif
                                            <tr class="border-bottom border-gray-100">
                                                <td class="py-16 px-16 text-center">
                                                    <button type="button"
                                                        class="acc-remove-cart text-2xl text-gray-400 hover-text-danger-600 transition-1 border-0 bg-transparent p-0"
                                                        data-url="{{ route('cart.remove', $item->id) }}"
                                                        title="Remove from Cart">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                </td>
                                                <td class="py-16 px-16">
                                                    <div class="table-product d-flex align-items-center gap-12">
                                                        <a href="/product/{{ $item->product->slug }}"
                                                            class="table-product__thumb border border-gray-100 rounded-8 p-4 flex-center flex-shrink-0 bg-white"
                                                            style="width: 56px; height: 56px;">
                                                            <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}"
                                                                alt="{{ $item->product->product_name }}"
                                                                class="w-100 h-100 object-fit-contain"
                                                                onerror="this.src='/assets/images/logo/logo.png'">
                                                        </a>
                                                        <div class="table-product__content text-start">
                                                            <h6 class="title text-base fw-semibold mb-4">
                                                                <a href="/product/{{ $item->product->slug }}" class="link text-line-2 hover-text-main-600">
                                                                    {{ $item->product->product_name }}
                                                                </a>
                                                            </h6>
                                                            @if($item->variant && !empty($item->variant->value) && $item->variant->unit)
                                                                <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-8 py-2 d-inline-block">
                                                                    {{ $item->variant->value }}{{ $item->variant->unit->short_name ?? '' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-16 px-16 text-nowrap">
                                                    <span class="text-base fw-semibold text-gray-900">
                                                        ₹{{ $item->variant->offer_price ?? $item->product->product_price }}
                                                    </span>
                                                </td>
                                                <td class="py-16 px-16 text-center text-nowrap">
                                                    <div class="d-inline-flex rounded-6 overflow-hidden border border-gray-200 acc-qty-box"
                                                        data-price="{{ $item->variant->offer_price ?? $item->product->product_price }}"
                                                        data-variant-id="{{ $item->variant->id ?? '' }}">
                                                        <button type="button" class="acc-qty-minus border-0 bg-gray-50 flex-shrink-0 h-32 w-32 text-gray-600 flex-center hover-bg-main-600 hover-text-white transition-1">
                                                            <i class="ph ph-minus"></i>
                                                        </button>
                                                        <input type="number" class="acc-qty-input border-0 text-center w-36 px-2 text-sm fw-semibold"
                                                            value="{{ $item->product_quantity }}" min="1" readonly>
                                                        <button type="button" class="acc-qty-plus border-0 bg-gray-50 flex-shrink-0 h-32 w-32 text-gray-600 flex-center hover-bg-main-600 hover-text-white transition-1">
                                                            <i class="ph ph-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="py-16 px-16 text-end text-nowrap">
                                                    <span class="acc-subtotal text-base fw-bold text-main-600">
                                                        ₹{{ ($item->variant->offer_price ?? $item->product->product_price ?? 0) * $item->product_quantity }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row justify-content-end mt-32">
                                <div class="col-sm-7 col-md-5">
                                    <div class="bg-gray-50 border border-gray-100 rounded-12 p-24">
                                        <h6 class="text-base fw-bold mb-16 text-gray-900">Cart Summary</h6>
                                        <div class="mb-12 flex-between gap-8 text-sm">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span class="text-gray-900 fw-bold acc-cart-subtotal">₹{{ number_format($cartSubtotal, 2) }}</span>
                                        </div>
                                        <div class="mb-12 flex-between gap-8 text-sm">
                                            <span class="text-gray-600">Estimated Shipping</span>
                                            <span class="text-success-600 fw-semibold">Calculated at checkout</span>
                                        </div>
                                        <div class="flex-between gap-8 pt-12 border-top border-gray-200 mt-12">
                                            <span class="text-gray-900 fw-bold">Total</span>
                                            <span class="text-main-600 text-lg fw-bold acc-cart-total">
                                                ₹{{ number_format($cartSubtotal, 2) }}
                                            </span>
                                        </div>
                                        <a href="/checkout" class="btn btn-main mt-20 py-12 w-100 rounded-8 fw-semibold flex-center gap-8">
                                            <span>Proceed to Checkout</span>
                                            <i class="ph-bold ph-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-60">
                                <span class="w-72 h-72 rounded-circle bg-gray-50 text-gray-400 flex-center mx-auto mb-16 text-36">
                                    <i class="ph ph-shopping-cart"></i>
                                </span>
                                <h6 class="text-lg fw-bold text-gray-900 mb-8">Your Cart is Empty</h6>
                                <p class="text-gray-500 text-sm mb-24 max-w-400 mx-auto">Looks like you haven't added any groceries to your cart yet.</p>
                                <a href="/products" class="btn btn-main py-12 px-32 rounded-8 text-sm fw-semibold">
                                    <span>Start Shopping</span>
                                    <i class="ph-bold ph-arrow-right ms-4"></i>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
                <!-- ========== Cart Tab End ========== -->

            </div>
            <!-- ===== Main Content End ===== -->

        </div>
    </div>
</section>
<!-- ================================ Account Section End ================================ -->

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

<style>
    .account-nav__link {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s ease;
        color: #4b5563;
        border: 1px solid transparent;
    }
    .account-nav__link:hover {
        background: #f0f7fa;
        color: var(--main-600, #1c799b) !important;
    }
    .account-nav__link.active-tab {
        background: #eef8fb;
        color: var(--main-600, #1c799b) !important;
        border-color: rgba(28, 121, 155, 0.15);
    }
    .account-nav__link.active-tab i {
        color: var(--main-600, #1c799b);
    }
    .cart-table th {
        color: #111827;
        font-weight: 600;
        letter-spacing: -0.2px;
    }
    .cart-table td {
        vertical-align: middle;
    }
</style>

@endsection

@push('scripts')
<script>
    function switchTab(e, tabName) {
        if (e) e.preventDefault();

        // Hide all tabs
        document.querySelectorAll('.account-tab').forEach(function(tab) {
            tab.style.display = 'none';
        });

        // Remove active from all nav links
        document.querySelectorAll('.account-nav__link').forEach(function(link) {
            link.classList.remove('active-tab');
        });

        // Show selected tab
        const targetTab = document.getElementById('tab-' + tabName);
        if (targetTab) {
            targetTab.style.display = 'block';
        }

        // Mark active nav link
        if (e && e.currentTarget) {
            e.currentTarget.classList.add('active-tab');
        }
    }

    function toggleAccountAddAddress() {
        const wrap = document.getElementById('account-add-address-wrap');
        const isHidden = wrap.style.display === 'none' || wrap.style.display === '';
        wrap.style.display = isHidden ? 'block' : 'none';
        const btnText = document.getElementById('acc-add-btn-text');
        if (btnText) {
            btnText.textContent = isHidden ? 'Close Form' : 'Add New Address';
        }
        if (isHidden) {
            wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function toggleAccountEdit(id) {
        const view = document.getElementById('acc-addr-view-' + id);
        const edit = document.getElementById('acc-addr-edit-' + id);
        if (!view || !edit) return;
        const isEditing = edit.style.display !== 'none';
        edit.style.display = isEditing ? 'none' : 'block';
        view.style.display = isEditing ? 'block' : 'none';
    }

    // Password toggles
    document.querySelectorAll(".toggle-password").forEach(icon => {
        icon.addEventListener("click", function () {
            const target = document.querySelector(this.getAttribute("id"));
            if (!target) return;
            if (target.type === "password") {
                target.type = "text";
                this.classList.remove("ph-eye-slash");
                this.classList.add("ph-eye");
            } else {
                target.type = "password";
                this.classList.remove("ph-eye");
                this.classList.add("ph-eye-slash");
            }
        });
    });

    const CSRF = document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}';

    // ── Wishlist remove ──────────────────────────────────────────────
    document.querySelectorAll('.remove-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id  = this.dataset.id;
            const row = this.closest('tr');

            fetch(`/wishlist/${id}/remove`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    Swal.fire({ icon: 'success', title: 'Removed from Wishlist', toast: true, position: 'top-end', showConfirmButton: false, timer: 1800 });
                }
            });
        });
    });

    // ── Wishlist → Add to Cart ───────────────────────────────────────
    document.querySelectorAll('.acc-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            const variantId = this.dataset.variantId;
            const self = this;

            fetch("{{ route('cart.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
            })
            .then(r => r.json())
            .then(data => {
                self.innerHTML = '<i class="ph-bold ph-check"></i> Added';
                self.disabled = true;
                Swal.fire({ icon: 'success', title: data.message || 'Added to cart!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            });
        });
    });

    // ── Cart remove ──────────────────────────────────────────────────
    document.querySelectorAll('.acc-remove-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.dataset.url;
            const row = this.closest('tr');

            fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    recalcCart();
                    Swal.fire({ icon: 'success', title: 'Item removed', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
                }
            });
        });
    });

    // ── Cart qty +/- ─────────────────────────────────────────────────
    document.querySelectorAll('.acc-qty-box').forEach(box => {
        const input     = box.querySelector('.acc-qty-input');
        const variantId = box.dataset.variantId;
        const subtotal  = box.closest('tr')?.querySelector('.acc-subtotal');
        const price     = parseFloat(box.dataset.price) || 0;

        box.querySelector('.acc-qty-plus')?.addEventListener('click', () => {
            const qty = (parseInt(input.value) || 1) + 1;
            syncAccCart(variantId, qty, input, subtotal, price);
        });

        box.querySelector('.acc-qty-minus')?.addEventListener('click', () => {
            const qty = parseInt(input.value) || 1;
            if (qty <= 1) return;
            syncAccCart(variantId, qty - 1, input, subtotal, price);
        });
    });

    function syncAccCart(variantId, qty, input, subtotalEl, price) {
        fetch("{{ route('cart.ajaxUpdate') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ product_varient_id: variantId, quantity: qty })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                input.value = qty;
                if (subtotalEl) subtotalEl.textContent = '₹' + (price * qty).toFixed(2);
                recalcCart();
            }
        });
    }

    function recalcCart() {
        let subtotal = 0;
        document.querySelectorAll('.acc-qty-box').forEach(box => {
            const price = parseFloat(box.dataset.price) || 0;
            const qty   = parseInt(box.querySelector('.acc-qty-input')?.value) || 1;
            subtotal += price * qty;
        });

        const subtotalEl = document.querySelector('.acc-cart-subtotal');
        const totalEl    = document.querySelector('.acc-cart-total');
        if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toFixed(2);
        if (totalEl)    totalEl.textContent    = '₹' + subtotal.toFixed(2);
    }
</script>
@endpush