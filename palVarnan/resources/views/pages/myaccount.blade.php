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
                        <i class="ph ph-house"></i>
                        Home
                    </a>
                </li>
                <li class="flex-align">
                    <i class="ph ph-caret-right"></i>
                </li>
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
                <div class="account-sidebar border border-gray-100 rounded-8 px-24 py-40">

                    <!-- User Info -->
                    <div class="text-center mb-32 pb-32" style="border-bottom: 1px solid #f0f0f0;">
                        <div class="w-80 h-80 rounded-circle bg-main-50 flex-center mx-auto mb-16"
                             style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#f0faf0;">
                            <i class="ph ph-user text-main-600" style="font-size:40px;"></i>
                        </div>
                        <h6 class="text-lg fw-semibold mb-4">
                            {{ Auth::user()->name }}
                        </h6>

                        <span class="text-sm text-gray-500">
                            {{ Auth::user()->email }}
                        </span>
                    </div>

                    <!-- Nav Menu -->
                    <ul class="account-nav d-flex flex-column gap-4" style="list-style:none;padding:0;margin:0;">
                        <li>
                            <a href="#" class="account-nav__link active-tab flex-align gap-12 px-16 py-12 rounded-8 text-gray-700 hover-text-main-600"
                               data-tab="orders" onclick="switchTab(event,'orders')">
                                <i class="ph ph-package text-xl"></i>
                                <span class="fw-medium">My Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 text-gray-700 hover-text-main-600"
                               data-tab="personal" onclick="switchTab(event,'personal')">
                                <i class="ph ph-user-circle text-xl"></i>
                                <span class="fw-medium">Personal Details</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 text-gray-700 hover-text-main-600"
                               data-tab="wishlist" onclick="switchTab(event,'wishlist')">
                                <i class="ph ph-heart text-xl"></i>
                                <span class="fw-medium">Wishlist</span>
                                <span class="ms-auto bg-main-600 text-white text-xs rounded-pill px-8 py-2"
                                    style="font-size:11px;padding:2px 8px;border-radius:20px;background:#22c55e;color:#fff;">
                                    {{ $wishlistItems->count() }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="account-nav__link flex-align gap-12 px-16 py-12 rounded-8 text-gray-700 hover-text-main-600"
                               data-tab="cart" onclick="switchTab(event,'cart')">
                                <i class="ph ph-shopping-cart text-xl"></i>
                                <span class="fw-medium">My Cart</span>
                                <span class="ms-auto bg-main-600 text-white text-xs rounded-pill px-8 py-2"
                                    style="font-size:11px;padding:2px 8px;border-radius:20px;background:#22c55e;color:#fff;">
                                    {{ $cartItems->count() }}
                                </span>
                            </a>
                        </li>
                        <li class="mt-16" style="border-top:1px solid #f0f0f0;padding-top:16px;">
                            <a href="/login" class="flex-align gap-12 px-16 py-12 rounded-8 text-danger-600 hover-text-danger-700" style="color:#dc2626;text-decoration:none;">
                                <i class="ph ph-sign-out text-xl"></i>
                                <span class="fw-medium">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- ===== Sidebar End ===== -->

            <!-- ===== Main Content Start ===== -->
            <div class="col-xl-9 col-lg-8">

                <!-- ========== Orders Tab ========== -->
                <div id="tab-orders" class="account-tab">
                    <div class="border border-gray-100 rounded-8 px-40 py-40">
                        <h6 class="text-xl mb-32">My Orders</h6>
                        <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                            <table class="table style-three">
                                <thead>
                                    <tr>
                                        <th class="h6 mb-0 text-lg fw-bold">Order ID</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Product Name</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Date</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Price</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Qty</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Total</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="text-main-600 fw-semibold">#ORD-1042</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-12">
                                                <img src="assets/images/cart/cart1.png" alt="" style="width:40px;height:40px;object-fit:contain;border:1px solid #f0f0f0;border-radius:6px;">
                                                <span class="text-gray-900 fw-medium">Red Chilli Powder</span>
                                            </div>
                                        </td>
                                        <td><span class="text-gray-700">12 Jan 2025</span></td>
                                        <td><span class="fw-semibold">₹125.00</span></td>
                                        <td><span class="text-gray-700">3</span></td>
                                        <td><span class="fw-semibold">₹375.00</span></td>
                                        <td><span style="background:#dcfce7;color:#16a34a;padding:4px 12px;border-radius:20px;font-size:13px;" class="fw-semibold">Delivered</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="text-main-600 fw-semibold">#ORD-1038</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-12">
                                                <img src="assets/images/cart/cart2.png" alt="" style="width:40px;height:40px;object-fit:contain;border:1px solid #f0f0f0;border-radius:6px;">
                                                <span class="text-gray-900 fw-medium">Cleaning Solution</span>
                                            </div>
                                        </td>
                                        <td><span class="text-gray-700">05 Jan 2025</span></td>
                                        <td><span class="fw-semibold">₹125.00</span></td>
                                        <td><span class="text-gray-700">1</span></td>
                                        <td><span class="fw-semibold">₹125.00</span></td>
                                        <td><span style="background:#fef9c3;color:#ca8a04;padding:4px 12px;border-radius:20px;font-size:13px;" class="fw-semibold">In Transit</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="text-main-600 fw-semibold">#ORD-1021</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-12">
                                                <img src="assets/images/cart/cart3.png" alt="" style="width:40px;height:40px;object-fit:contain;border:1px solid #f0f0f0;border-radius:6px;">
                                                <span class="text-gray-900 fw-medium">Potato</span>
                                            </div>
                                        </td>
                                        <td><span class="text-gray-700">28 Dec 2024</span></td>
                                        <td><span class="fw-semibold">₹130.00</span></td>
                                        <td><span class="text-gray-700">5</span></td>
                                        <td><span class="fw-semibold">₹650.00</span></td>
                                        <td><span style="background:#dcfce7;color:#16a34a;padding:4px 12px;border-radius:20px;font-size:13px;" class="fw-semibold">Delivered</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="text-main-600 fw-semibold">#ORD-1010</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-12">
                                                <img src="assets/images/cart/cart1.png" alt="" style="width:40px;height:40px;object-fit:contain;border:1px solid #f0f0f0;border-radius:6px;">
                                                <span class="text-gray-900 fw-medium">Red Chilli Powder</span>
                                            </div>
                                        </td>
                                        <td><span class="text-gray-700">15 Dec 2024</span></td>
                                        <td><span class="fw-semibold">₹125.00</span></td>
                                        <td><span class="text-gray-700">2</span></td>
                                        <td><span class="fw-semibold">₹250.00</span></td>
                                        <td><span style="background:#fee2e2;color:#dc2626;padding:4px 12px;border-radius:20px;font-size:13px;" class="fw-semibold">Cancelled</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- ========== Orders Tab End ========== -->

                <!-- ========== Personal Details Tab ========== -->
                <div id="tab-personal" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-8 px-40 py-40">
                        <h6 class="text-xl mb-32">Personal Details</h6>
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row gy-24">
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="common-input" value="{{ old('name', Auth::user()->name) }}">
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="common-input" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}">
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="common-input" name="email" value="{{ old('email', Auth::user()->email) }}">
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="common-input" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}">
                                </div>
                                <!-- <div class="col-12">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block">Address</label>
                                    <input type="text" class="common-input" value="123, Main Street, Coimbatore" placeholder="Full Address">
                                </div>
                                <div class="col-sm-4">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block">City</label>
                                    <input type="text" class="common-input" value="Coimbatore" placeholder="City">
                                </div>
                                <div class="col-sm-4">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block">State</label>
                                    <input type="text" class="common-input" value="Tamil Nadu" placeholder="State">
                                </div>
                                <div class="col-sm-4">
                                    <label class="text-neutral-900 text-lg mb-8 fw-medium d-block">Pincode</label>
                                    <input type="text" class="common-input" value="641001" placeholder="Pincode">
                                </div> -->
                            </div>

                            <!-- Change Password -->
                            <div class="mt-40 pt-32" style="border-top:1px solid #f0f0f0;">
                                <h6 class="text-lg fw-semibold mb-24">Change Password</h6>
                                <div class="row gy-24">
                                    <div class="col-sm-6">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">Current Password</label>
                                        <input type="password" name="current_password" class="common-input" placeholder="Current Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">New Password</label>
                                        <input type="password" name="password" class="common-input" placeholder="New Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="text-neutral-900 text-lg mb-8 fw-medium d-block mt-20">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="common-input" placeholder="Confirm New Password">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-32">
                                <button type="submit" class="btn btn-main py-18 px-40 rounded-8">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- ========== Personal Details Tab End ========== -->

                <!-- ========== Wishlist Tab ========== -->
                <div id="tab-wishlist" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-8 px-40 py-40">
                        <h6 class="text-xl mb-32">My Wishlist</h6>
                        <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                            <table class="table style-three">
                                <thead>
                                    <tr>
                                        <th class="h6 mb-0 text-lg fw-bold">Delete</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Product</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Variant</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Price</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($wishlistItems as $item)
                                    @php $wVariant = $item->variant; @endphp
                                    <tr>
                                        <td>
                                            <button type="button"
                                                class="remove-wishlist-btn flex-align gap-12 hover-text-danger-600"
                                                data-id="{{ $item->id }}"
                                                style="background:none;border:none;cursor:pointer;">
                                                <i class="ph ph-x-circle text-2xl d-flex"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="table-product d-flex align-items-center gap-12">
                                                <a href="/product/{{ $item->product->slug }}"
                                                    class="table-product__thumb border border-gray-100 rounded-8 flex-center flex-shrink-0">
                                                    <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}"
                                                        alt="" style="width:48px;height:48px;object-fit:cover;">
                                                </a>
                                                <div class="table-product__content text-start">
                                                    <h6 class="title text-lg fw-semibold mb-0">
                                                        <a href="/product/{{ $item->product->slug }}" class="link text-line-2">
                                                            {{ $item->product->product_name }}
                                                        </a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($wVariant && !empty($wVariant->value) && $wVariant->unit)
                                                <span class="text-sm fw-semibold text-gray-700 border border-gray-100 rounded-8 px-10 py-4 d-inline-block">
                                                    {{ $wVariant->value }}{{ $wVariant->unit->short_name ?? '' }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($wVariant && $wVariant->mrp_price)
                                                <span class="text-sm text-gray-400 text-decoration-line-through d-block">
                                                    ₹{{ $wVariant->mrp_price }}
                                                </span>
                                            @endif
                                            <span class="text-lg h6 mb-0 fw-semibold">
                                                ₹{{ $wVariant->offer_price ?? '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="acc-add-to-cart btn btn-main py-8 px-24 rounded-8"
                                                data-product-id="{{ $item->product->id }}"
                                                data-variant-id="{{ $item->product_varient_id }}">
                                                Add to Cart
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-40 text-gray-500">
                                            <i class="ph ph-heart text-4xl d-block mb-8"></i>
                                            Your wishlist is empty.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========== Cart Tab ========== -->
                <div id="tab-cart" class="account-tab" style="display:none;">
                    <div class="border border-gray-100 rounded-8 px-40 py-40">
                        <h6 class="text-xl mb-32">My Cart</h6>
                        <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                            <table class="table style-three">
                                <thead>
                                    <tr>
                                        <th class="h6 mb-0 text-lg fw-bold">Delete</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Product</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Price</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Quantity</th>
                                        <th class="h6 mb-0 text-lg fw-bold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cartItems as $item)
                                    <tr>
                                        <td>
                                            <button type="button"
                                                class="acc-remove-cart flex-align gap-12 hover-text-danger-600"
                                                data-url="{{ route('cart.remove', $item->id) }}"
                                                style="background:none;border:none;cursor:pointer;">
                                                <i class="ph ph-x-circle text-2xl d-flex"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="table-product d-flex align-items-center gap-12">
                                                <a href="/product/{{ $item->product->slug }}"
                                                    class="table-product__thumb border border-gray-100 rounded-8 flex-center flex-shrink-0">
                                                    <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}"
                                                        alt="" style="width:48px;height:48px;object-fit:cover;">
                                                </a>
                                                <div class="table-product__content text-start">
                                                    <h6 class="title text-lg fw-semibold mb-4">
                                                        <a href="/product/{{ $item->product->slug }}" class="link text-line-2">
                                                            {{ $item->product->product_name }}
                                                        </a>
                                                    </h6>
                                                    @if($item->variant && !empty($item->variant->value) && $item->variant->unit)
                                                        <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-10 py-2 d-inline-block">
                                                            {{ $item->variant->value }}{{ $item->variant->unit->short_name ?? '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-lg h6 mb-0 fw-semibold">
                                                ₹{{ $item->variant->offer_price }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex rounded-4 overflow-hidden acc-qty-box"
                                                data-price="{{ $item->variant->offer_price }}"
                                                data-variant-id="{{ $item->variant->id }}">
                                                <button type="button" class="acc-qty-minus border border-end border-gray-100 flex-shrink-0 h-36 w-36 text-neutral-600 flex-center hover-bg-main-600 hover-text-white">
                                                    <i class="ph ph-minus"></i>
                                                </button>
                                                <input type="number" class="acc-qty-input flex-grow-1 border border-gray-100 border-start-0 border-end-0 text-center w-24 px-2"
                                                    value="{{ $item->product_quantity }}" min="1" readonly>
                                                <button type="button" class="acc-qty-plus border border-start border-gray-100 flex-shrink-0 h-36 w-36 text-neutral-600 flex-center hover-bg-main-600 hover-text-white">
                                                    <i class="ph ph-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="acc-subtotal text-lg h6 mb-0 fw-semibold">
                                                ₹{{ $item->variant->offer_price * $item->product_quantity }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-40 text-gray-500">
                                            <i class="ph ph-shopping-cart text-4xl d-block mb-8"></i>
                                            Your cart is empty.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($cartItems->count() > 0)
                        <div class="row justify-content-end mt-32">
                            <div class="col-sm-6 col-md-4">
                                <div class="bg-color-three rounded-8 p-24">
                                    <div class="mb-16 flex-between gap-8">
                                        <span class="text-gray-900 font-heading-two">Subtotal</span>
                                        <span class="text-gray-900 fw-semibold acc-cart-subtotal">₹{{ number_format($cartSubtotal, 2) }}</span>
                                    </div>
                                    <div class="mb-16 flex-between gap-8">
                                        <span class="text-gray-900 font-heading-two">Delivery</span>
                                        <span class="text-gray-900 fw-semibold">₹50.00</span>
                                    </div>
                                    <div class="flex-between gap-8" style="border-top:1px solid #e5e7eb;padding-top:16px;margin-top:8px;">
                                        <span class="text-gray-900 text-lg fw-semibold">Total</span>
                                        <span class="text-gray-900 text-lg fw-semibold acc-cart-total">
                                            ₹{{ number_format($cartSubtotal + 50 + ($cartSubtotal * 0.16), 2) }}
                                        </span>
                                    </div>
                                </div>
                                <a href="/checkout" class="btn btn-main mt-16 py-18 w-100 rounded-8">Proceed to Checkout</a>
                            </div>
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


<!-- ========================== Account Tab Styles & Script ============================ -->
<style>
    .account-nav__link {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }
    .account-nav__link:hover,
    .account-nav__link.active-tab {
        background: #f0fdf4;
        color: #16a34a !important;
    }
    .account-nav__link.active-tab i {
        color: #16a34a;
    }
    .product-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
</style>

<script>
    function switchTab(e, tabName) {
        e.preventDefault();

        // Hide all tabs
        document.querySelectorAll('.account-tab').forEach(function(tab) {
            tab.style.display = 'none';
        });

        // Remove active from all nav links
        document.querySelectorAll('.account-nav__link').forEach(function(link) {
            link.classList.remove('active-tab');
        });

        // Show selected tab
        document.getElementById('tab-' + tabName).style.display = 'block';

        // Mark active nav link
        e.currentTarget.classList.add('active-tab');
    }
</script>

<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

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
            self.textContent = '✓ Added';
            self.disabled = true;
            Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
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
                Swal.fire({ icon: 'success', title: 'Removed!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
            }
        });
    });
});

// ── Cart qty +/- ─────────────────────────────────────────────────
document.querySelectorAll('.acc-qty-box').forEach(box => {
    const input     = box.querySelector('.acc-qty-input');
    const variantId = box.dataset.variantId;
    const subtotal  = box.closest('tr').querySelector('.acc-subtotal');
    const price     = parseFloat(box.dataset.price);

    box.querySelector('.acc-qty-plus').addEventListener('click', () => {
        const qty = parseInt(input.value) + 1;
        syncAccCart(variantId, qty, input, subtotal, price);
    });

    box.querySelector('.acc-qty-minus').addEventListener('click', () => {
        const qty = parseInt(input.value);
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
            subtotalEl.textContent = '₹' + (price * qty).toFixed(2);
            recalcCart();
        }
    });
}

function recalcCart() {
    let subtotal = 0;
    document.querySelectorAll('.acc-qty-box').forEach(box => {
        const price = parseFloat(box.dataset.price);
        const qty   = parseInt(box.querySelector('.acc-qty-input').value) || 1;
        subtotal += price * qty;
    });

    const subtotalEl = document.querySelector('.acc-cart-subtotal');
    const totalEl    = document.querySelector('.acc-cart-total');
    if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toFixed(2);
    if (totalEl)    totalEl.textContent    = '₹' + (subtotal + 50 + subtotal * 0.16).toFixed(2);
}
</script>

@endsection