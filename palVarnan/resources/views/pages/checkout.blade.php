@extends('layouts.app')
@section('main')

<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Checkout</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                        <i class="ph ph-house"></i> Home
                    </a>
                </li>
                <li class="flex-align"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-main-600">Checkout</li>
            </ul>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="container container-lg mt-24"><div class="alert alert-success">{{ session('success') }}</div></div>
@endif
@if (session('error'))
    <div class="container container-lg mt-24"><div class="alert alert-danger">{{ session('error') }}</div></div>
@endif

<style>
#toast-container {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
}
.app-toast {
    pointer-events: auto;
    min-width: 300px;
    max-width: 440px;
    padding: 16px 20px;
    border-radius: 10px;
    background: #ffffff !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18), 0 4px 6px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 14px;
    font-weight: 600;
    color: #111827 !important;
    opacity: 0;
    transform: translateY(-15px) scale(0.95);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.app-toast span {
    color: #111827 !important;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
}
.app-toast i {
    font-size: 22px;
    flex-shrink: 0;
}
.app-toast--show {
    opacity: 1;
    transform: translateY(0) scale(1);
}
.app-toast--success {
    border-left: 5px solid #10b981;
}
.app-toast--success i {
    color: #10b981;
}
.app-toast--error {
    border-left: 5px solid #ef4444;
}
.app-toast--error i {
    color: #ef4444;
}
.app-toast--info {
    border-left: 5px solid #3b82f6;
}
.app-toast--info i {
    color: #3b82f6;
}
</style>

<section class="checkout py-80 addr-premium">
    <div class="container container-lg">
        <div class="border border-gray-100 rounded-8 px-30 py-20 mb-40">
            <span>Have a coupon? <a href="/cart" class="fw-semibold text-gray-900 hover-text-decoration-underline hover-text-main-600">Click here to enter your code</a></span>
        </div>

        <div class="row">
            <div class="col-xl-9 col-lg-8">

                <div class="flex-between align-items-center mb-24">
                    <h6 class="mb-0 addr-section-title">Deliver to</h6>
                </div>

                @foreach ($addresses as $addr)
                    @php
                        $isEditing = old('_editing_id') == $addr->id && $errors->any();
                        $typeVal = old('address_type_id', $addr->address_type_id);
                    @endphp

                    <div class="addr-card {{ (int) $selectedAddressId === $addr->id ? 'addr-card--selected' : '' }} mb-16">

                        {{-- Display state --}}
                        <div class="addr-card__view" id="view-{{ $addr->id }}" style="{{ $isEditing ? 'display:none;' : '' }}">
                            <label class="addr-card__label">
                                <input type="radio" name="address_id" value="{{ $addr->id }}" class="addr-radio"
                                       {{ (int) $selectedAddressId === $addr->id ? 'checked' : '' }}
                                       onchange="window.location='{{ route('checkout.index') }}?address_id={{ $addr->id }}'">
                                <span class="addr-radio__dot"></span>

                                <div class="addr-card__body">
                                    <div class="flex-between align-items-start">
                                        <div class="flex-align gap-10">
                                            <span class="addr-card__name">{{ $addr->address_first_name }} {{ $addr->address_last_name }}</span>
                                            <span class="addr-badge">
                                                @if($addr->address_type_id == 1) <i class="ph-fill ph-house-line"></i> Home
                                                @elseif($addr->address_type_id == 2) <i class="ph-fill ph-briefcase"></i> Work
                                                @else <i class="ph-fill ph-map-pin"></i> {{ $addr->address_type_others_name ?: 'Other' }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-align gap-8">
                                            <button type="button" class="addr-edit-btn" onclick="toggleEdit({{ $addr->id }})">
                                                <i class="ph ph-pencil-simple"></i> Edit
                                            </button>
                                            <form action="{{ route('address.destroy', $addr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="addr-edit-btn text-danger" style="color: #ef4444;" title="Delete Address">
                                                    <i class="ph ph-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <p class="addr-card__text">
                                        {{ $addr->address_line_one }}{{ $addr->address_line_two ? ', '.$addr->address_line_two : '' }}
                                        @if($addr->landmark), near {{ $addr->landmark }} @endif
                                    </p>
                                    <p class="addr-card__text">{{ $addr->city }}, {{ $addr->state }} — {{ $addr->pincode }}</p>
                                    <p class="addr-card__meta">
                                        <i class="ph ph-phone"></i> {{ $addr->address_phone_number }}
                                        <span class="addr-dot">•</span>
                                        <i class="ph ph-envelope-simple"></i> {{ $addr->email }}
                                    </p>
                                </div>
                            </label>
                        </div>

                        {{-- Inline edit form --}}
                        <div class="addr-card__edit" id="edit-{{ $addr->id }}" style="{{ $isEditing ? '' : 'display:none;' }}">
                            <form action="{{ route('address.update', $addr->id) }}" method="POST" class="addr-form">
                                @csrf @method('PUT')
                                <input type="hidden" name="_editing_id" value="{{ $addr->id }}">

                                <div class="row gy-3">
                                    <div class="col-sm-6">
                                        <input type="text" name="first_name" placeholder="First Name"
                                               class="common-input border-gray-100 @error('first_name') is-invalid @enderror"
                                               value="{{ $isEditing ? old('first_name') : $addr->address_first_name }}">
                                        @if($isEditing) @error('first_name') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="last_name" placeholder="Last Name"
                                               class="common-input border-gray-100 @error('last_name') is-invalid @enderror"
                                               value="{{ $isEditing ? old('last_name') : $addr->address_last_name }}">
                                        @if($isEditing) @error('last_name') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="tel" name="phone_number" placeholder="Phone Number"
                                               class="common-input border-gray-100 @error('phone_number') is-invalid @enderror"
                                               value="{{ $isEditing ? old('phone_number') : $addr->address_phone_number }}">
                                        @if($isEditing) @error('phone_number') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="email" name="email" placeholder="Email Address"
                                               class="common-input border-gray-100 @error('email') is-invalid @enderror"
                                               value="{{ $isEditing ? old('email') : $addr->email }}">
                                        @if($isEditing) @error('email') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="address_line_one" placeholder="House number and street name"
                                               class="common-input border-gray-100 @error('address_line_one') is-invalid @enderror"
                                               value="{{ $isEditing ? old('address_line_one') : $addr->address_line_one }}">
                                        @if($isEditing) @error('address_line_one') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="address_line_two" placeholder="Apartment, suite, unit, etc. (Optional)"
                                               class="common-input border-gray-100"
                                               value="{{ $isEditing ? old('address_line_two') : $addr->address_line_two }}">
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="landmark" placeholder="Landmark (Optional)"
                                               class="common-input border-gray-100"
                                               value="{{ $isEditing ? old('landmark') : $addr->landmark }}">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="city" placeholder="City"
                                               class="common-input border-gray-100 @error('city') is-invalid @enderror"
                                               value="{{ $isEditing ? old('city') : $addr->city }}">
                                        @if($isEditing) @error('city') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="state" placeholder="State"
                                               class="common-input border-gray-100 @error('state') is-invalid @enderror"
                                               value="{{ $isEditing ? old('state') : $addr->state }}">
                                        @if($isEditing) @error('state') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="pincode" placeholder="Pincode"
                                               class="common-input border-gray-100 @error('pincode') is-invalid @enderror"
                                               value="{{ $isEditing ? old('pincode') : $addr->pincode }}">
                                        @if($isEditing) @error('pincode') <span class="invalid-text">{{ $message }}</span> @enderror @endif
                                    </div>
                                    <!-- <div class="col-12">
                                        <div class="addr-type-group">
                                            <label class="addr-type-option">
                                                <input type="radio" name="address_type_id" value="1" {{ $typeVal == 1 ? 'checked' : '' }}>
                                                <span><i class="ph-fill ph-house-line"></i> Home</span>
                                            </label>
                                            <label class="addr-type-option">
                                                <input type="radio" name="address_type_id" value="2" {{ $typeVal == 2 ? 'checked' : '' }}>
                                                <span><i class="ph-fill ph-briefcase"></i> Work</span>
                                            </label>
                                            <label class="addr-type-option">
                                                <input type="radio" name="address_type_id" value="3" {{ $typeVal == 3 ? 'checked' : '' }}>
                                                <span><i class="ph-fill ph-map-pin"></i> Other</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="address_type_others_name" placeholder="Label this address (e.g. Mom's House)"
                                               class="common-input border-gray-100"
                                               value="{{ $isEditing ? old('address_type_others_name') : $addr->address_type_others_name }}">
                                    </div> -->
                                </div>

                                <div class="addr-form__actions">
                                    <button type="button" class="addr-btn addr-btn--ghost" onclick="toggleEdit({{ $addr->id }})">Cancel</button>
                                    <button type="submit" class="addr-btn addr-btn--primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                @if ($addresses->isEmpty())
                    <p class="text-gray-500 mb-16">You have no saved addresses yet.</p>
                @endif

                {{-- Add new address --}}
                @php
                    $showAddForm = old('_editing_id') === null && $errors->any();
                    $addTypeVal = old('address_type_id', 1);
                @endphp

                <div class="addr-add-toggle {{ $showAddForm ? 'd-none' : '' }}" id="add-toggle" onclick="toggleAdd()">
                    <i class="ph ph-plus-circle"></i>
                    <span>Add a new address</span>
                </div>

                <div class="addr-card addr-card--new {{ $showAddForm ? '' : 'd-none' }}" id="add-form-wrap">
                    <div class="addr-card__edit">
                        <form action="{{ route('address.store') }}" method="POST" class="addr-form">
                            @csrf

                            <div class="row gy-3">
                                <div class="col-sm-6">
                                    <input type="text" name="first_name" placeholder="First Name"
                                           class="common-input border-gray-100 @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name', $user->name ?? '') }}">
                                    @error('first_name') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="last_name" placeholder="Last Name"
                                           class="common-input border-gray-100 @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name') }}">
                                    @error('last_name') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="tel" name="phone_number" placeholder="Phone Number"
                                           class="common-input border-gray-100 @error('phone_number') is-invalid @enderror"
                                           value="{{ old('phone_number', $user->phone_number ?? '') }}">
                                    @error('phone_number') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="email" name="email" placeholder="Email Address"
                                           class="common-input border-gray-100 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email ?? '') }}">
                                    @error('email') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <input type="text" name="address_line_one" placeholder="House number and street name"
                                           class="common-input border-gray-100 @error('address_line_one') is-invalid @enderror"
                                           value="{{ old('address_line_one') }}">
                                    @error('address_line_one') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <input type="text" name="address_line_two" placeholder="Apartment, suite, unit, etc. (Optional)"
                                           class="common-input border-gray-100"
                                           value="{{ old('address_line_two') }}">
                                </div>
                                <div class="col-12">
                                    <input type="text" name="landmark" placeholder="Landmark (Optional)"
                                           class="common-input border-gray-100"
                                           value="{{ old('landmark') }}">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="city" placeholder="City"
                                           class="common-input border-gray-100 @error('city') is-invalid @enderror"
                                           value="{{ old('city') }}">
                                    @error('city') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="state" placeholder="State"
                                           class="common-input border-gray-100 @error('state') is-invalid @enderror"
                                           value="{{ old('state') }}">
                                    @error('state') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="pincode" placeholder="Pincode"
                                           class="common-input border-gray-100 @error('pincode') is-invalid @enderror"
                                           value="{{ old('pincode') }}">
                                    @error('pincode') <span class="invalid-text">{{ $message }}</span> @enderror
                                </div>
                                <!-- <div class="col-12">
                                    <div class="addr-type-group">
                                        <label class="addr-type-option">
                                            <input type="radio" name="address_type_id" value="1" {{ $addTypeVal == 1 ? 'checked' : '' }}>
                                            <span><i class="ph-fill ph-house-line"></i> Home</span>
                                        </label>
                                        <label class="addr-type-option">
                                            <input type="radio" name="address_type_id" value="2" {{ $addTypeVal == 2 ? 'checked' : '' }}>
                                            <span><i class="ph-fill ph-briefcase"></i> Work</span>
                                        </label>
                                        <label class="addr-type-option">
                                            <input type="radio" name="address_type_id" value="3" {{ $addTypeVal == 3 ? 'checked' : '' }}>
                                            <span><i class="ph-fill ph-map-pin"></i> Other</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="address_type_others_name" placeholder="Label this address (e.g. Mom's House)"
                                           class="common-input border-gray-100"
                                           value="{{ old('address_type_others_name') }}">
                                </div> -->
                            </div>

                            <div class="addr-form__actions">
                                <button type="button" class="addr-btn addr-btn--ghost" onclick="toggleAdd()">Cancel</button>
                                <button type="submit" class="addr-btn addr-btn--primary">Save Address</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Courier Partner Selection --}}
                <div class="mt-32 border border-gray-100 rounded-12 p-24 bg-white mb-24" id="courier-section">
                    <h6 class="mb-16 font-heading-two text-lg fw-semibold flex-align gap-8">
                        <i class="ph-bold ph-truck text-main-600"></i> Select Delivery Partner
                    </h6>
                    <div id="courier-loading" class="text-gray-500 py-12 flex-align gap-8">
                        <div class="spinner-border spinner-border-sm text-main-600" role="status"></div>
                        <span>Checking available couriers for your delivery location...</span>
                    </div>
                    <div id="couriers-list" class="row gy-3 mt-8" style="display: none;"></div>
                    <div id="no-couriers" class="alert alert-warning py-12 px-16 text-sm mb-0" style="display: none;">
                        <i class="ph ph-warning-circle me-4"></i> No delivery partners found for this pincode. Standard shipping will be applied.
                    </div>
                </div>

                {{-- Payment Method Selection --}}
                <div class="border border-gray-100 rounded-12 p-24 bg-white mb-32">
                    <h6 class="mb-16 font-heading-two text-lg fw-semibold flex-align gap-8">
                        <i class="ph-bold ph-credit-card text-main-600"></i> Select Payment Method
                    </h6>
                    <div class="row gy-3">
                        <div class="col-sm-6">
                            <label class="border border-gray-100 rounded-10 p-16 d-flex align-items-center gap-12 cursor-pointer payment-option-card active" style="border: 2px solid #10b981;">
                                <input type="radio" name="payment_method_radio" value="Online Payment" checked class="form-check-input mt-0 payment-radio" onchange="changePaymentMethod('Online Payment')">
                                <div>
                                    <span class="d-block fw-semibold text-gray-900">Online Payment</span>
                                    <span class="text-xs text-gray-500">UPI, Cards, NetBanking, Wallets (Razorpay)</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="border border-gray-100 rounded-10 p-16 d-flex align-items-center gap-12 cursor-pointer payment-option-card" style="border: 1px solid #e5e7eb;">
                                <input type="radio" name="payment_method_radio" value="Cash on Delivery" class="form-check-input mt-0 payment-radio" onchange="changePaymentMethod('Cash on Delivery')">
                                <div>
                                    <span class="d-block fw-semibold text-gray-900">Cash on Delivery</span>
                                    <span class="text-xs text-gray-500">Pay when you receive the order</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

            </div> {{-- End col-xl-9 col-lg-8 --}}

            {{-- Right Sidebar --}}
            <div class="col-xl-3 col-lg-4">
                <div class="checkout-sidebar">
                    <div class="bg-color-three rounded-8 p-20 text-center" style="background-color: #f3f4f6;">
                        <span class="text-gray-900 text-xl fw-bold font-heading-two">Your Orders</span>
                    </div>

                    <div class="border border-gray-100 rounded-8 px-24 py-32 mt-20 bg-white">
                        <div class="mb-24 pb-16 border-bottom border-gray-100 flex-between gap-8">
                            <span class="text-gray-900 fw-bold text-lg font-heading-two">Product</span>
                            <span class="text-gray-900 fw-bold text-lg font-heading-two">Subtotal</span>
                        </div>

                        @forelse ($cartItems as $item)
                            <div class="flex-between gap-16 mb-20">
                                <div class="flex-align gap-8">
                                    <span class="text-gray-900 fw-medium text-sm font-heading-two line-clamp-1" style="max-width: 140px;">
                                        {{ $item->product->product_name ?? 'Product' }}
                                    </span>
                                    <span class="text-gray-500 text-xs fw-semibold">× {{ $item->product_quantity }}</span>
                                </div>
                                <span class="text-gray-900 fw-bold text-sm font-heading-two">
                                    ₹{{ number_format(($item->variant->offer_price ?? $item->product->product_price ?? 0) * $item->product_quantity, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Your cart is empty.</p>
                        @endforelse

                        <div class="border-top border-gray-100 pt-20 mt-20">
                            <div class="mb-12 flex-between gap-8">
                                <span class="text-gray-900 fw-semibold text-md font-heading-two">Subtotal</span>
                                <span class="text-gray-900 font-heading-two text-md fw-bold">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($totalGst > 0)
                            <div class="mb-12 flex-between gap-8">
                                <span class="text-gray-600 text-sm">Estimated GST</span>
                                <span class="text-gray-900 font-heading-two text-sm fw-bold">₹{{ number_format($totalGst, 2) }}</span>
                            </div>
                            @endif
                            <div class="mb-12 flex-between gap-8">
                                <span class="text-gray-600 text-sm">Shipping Charge</span>
                                <span class="text-gray-900 font-heading-two text-sm fw-bold" id="display-shipping-charge">₹0.00</span>
                            </div>
                            <div class="mb-0 pt-16 border-top border-gray-100 flex-between gap-8">
                                <span class="text-gray-900 font-heading-two text-lg fw-bold">Total</span>
                                <span class="text-gray-900 font-heading-two text-xl fw-bold" id="display-grand-total">
                                    ₹{{ number_format($subtotal + $totalGst, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-24 pt-20 border-top border-gray-100">
                        <p class="text-gray-500 text-xs">Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#" class="text-main-600 text-decoration-underline">privacy policy</a>.</p>
                    </div>

                    <form id="place-order-form">
                        @csrf
                        <input type="hidden" name="address_id" id="hidden_address_id" value="{{ $selectedAddressId }}">
                        <input type="hidden" name="payment_method" id="hidden_payment_method" value="Online Payment">
                        <input type="hidden" name="selected_courier_id" id="hidden_courier_id" value="">
                        <input type="hidden" name="selected_courier_name" id="hidden_courier_name" value="">
                        <input type="hidden" name="shipping_charge" id="hidden_shipping_charge" value="0">
                        <input type="hidden" name="coupon_discount" id="hidden_coupon_discount" value="0">

                        {{-- Snapshot address fields --}}
                        <input type="hidden" name="shipping_name" id="snap_name" value="{{ $selectedAddress ? ($selectedAddress->address_first_name . ' ' . $selectedAddress->address_last_name) : ($user->name ?? '') }}">
                        <input type="hidden" name="shipping_email" id="snap_email" value="{{ $selectedAddress->email ?? $user->email }}">
                        <input type="hidden" name="shipping_phone" id="snap_phone" value="{{ $selectedAddress->address_phone_number ?? $user->phone_number }}">
                        <input type="hidden" name="shipping_door_no" id="snap_door" value="{{ $selectedAddress->address_line_one ?? '' }}">
                        <input type="hidden" name="shipping_street" id="snap_street" value="{{ $selectedAddress->address_line_two ?? '' }}">
                        <input type="hidden" name="shipping_area" id="snap_area" value="{{ $selectedAddress->landmark ?? '' }}">
                        <input type="hidden" name="shipping_city" id="snap_city" value="{{ $selectedAddress->city ?? '' }}">
                        <input type="hidden" name="shipping_state" id="snap_state" value="{{ $selectedAddress->state ?? '' }}">
                        <input type="hidden" name="shipping_pincode" id="snap_pincode" value="{{ $selectedAddress->pincode ?? '' }}">

                        <button type="button" id="btn-place-order" class="btn btn-main py-16 w-100 rounded-8 mt-24 fw-semibold text-md" style="background-color: #176983; color: #ffffff;" {{ $addresses->isEmpty() ? 'disabled' : '' }} onclick="handlePlaceOrderClick()">
                            <span id="btn-order-text">Place Order</span>
                            <span id="btn-order-spinner" class="spinner-border spinner-border-sm ms-8" style="display: none;" role="status"></span>
                        </button>
                    </form>
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



<div id="toast-container"></div>

<script>
function toggleEdit(id) {
    const view = document.getElementById('view-' + id);
    const edit = document.getElementById('edit-' + id);
    const opening = edit.style.display === 'none';
    edit.style.display = opening ? 'block' : 'none';
    view.style.display = opening ? 'none' : 'block';
}

function toggleAdd() {
    const wrap = document.getElementById('add-form-wrap');
    const toggle = document.getElementById('add-toggle');
    const opening = wrap.classList.contains('d-none');
    wrap.classList.toggle('d-none', !opening);
    toggle.classList.toggle('d-none', opening);
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `app-toast app-toast--${type}`;
    toast.innerHTML = `
        <i class="ph-fill ${type === 'success' ? 'ph-check-circle' : 'ph-x-circle'}"></i>
        <span>${message}</span>
    `;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('app-toast--show'));
    setTimeout(() => {
        toast.classList.remove('app-toast--show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function clearFormErrors(form) {
    form.querySelectorAll('.invalid-text').forEach(el => el.remove());
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFormErrors(form, errors) {
    Object.keys(errors).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;
        input.classList.add('is-invalid');
        const span = document.createElement('span');
        span.className = 'invalid-text';
        span.textContent = errors[field][0];
        input.insertAdjacentElement('afterend', span);
    });
}

async function submitAddressForm(form) {
    clearFormErrors(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content
            || form.querySelector('input[name="_token"]')?.value;

        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: new FormData(form),
        });

        if (res.status === 419) {
            showToast('Session expired. Refreshing page...', 'error');
            setTimeout(() => window.location.reload(), 1000);
            return;
        }

        let data = {};
        try {
            data = await res.json();
        } catch (e) {
            data = { message: 'Server response error.' };
        }

        if (res.ok && data.success) {
            showToast(data.message || 'Address saved successfully.');
            setTimeout(() => {
                const addressId = data.address?.id;
                if (addressId) {
                    window.location.href = "{{ route('checkout.index') }}?address_id=" + addressId;
                } else {
                    window.location.reload();
                }
            }, 600);
        } else if (res.status === 422) {
            showFormErrors(form, data.errors || {});
            showToast(data.message || 'Please fix the highlighted fields.', 'error');
        } else {
            showToast(data.message || 'Could not save address. Please check input.', 'error');
        }
    } catch (err) {
        console.error('Address save error:', err);
        showToast('Network error. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.classList.contains('addr-form')) {
        e.preventDefault();
        submitAddressForm(form);
    }
});

// ─── Razorpay & Shiprocket Checkout Integration ───
const baseSubtotalWithGst = {{ $subtotal + $totalGst }};
let currentShippingCharge = 0;
let currentDiscount = 0;
let activePaymentMethod = 'Online Payment';
const selectedPincode = "{{ $selectedAddress->pincode ?? '' }}";

function updateTotals() {
    const grandTotal = Math.max((baseSubtotalWithGst + currentShippingCharge) - currentDiscount, 0);
    document.getElementById('display-shipping-charge').innerText = '₹' + currentShippingCharge.toFixed(2);
    document.getElementById('display-grand-total').innerText = '₹' + grandTotal.toFixed(2);
    document.getElementById('hidden_shipping_charge').value = currentShippingCharge;
}

function changePaymentMethod(method) {
    activePaymentMethod = method;
    document.getElementById('hidden_payment_method').value = method;

    document.querySelectorAll('.payment-option-card').forEach(card => {
        const input = card.querySelector('input');
        if (input.value === method) {
            card.style.border = '2px solid #10b981';
            card.classList.add('active');
        } else {
            card.style.border = '1px solid #e5e7eb';
            card.classList.remove('active');
        }
    });

    if (selectedPincode) {
        fetchCouriers(selectedPincode, method);
    }
}

async function fetchCouriers(pincode, paymentMethod) {
    const loadingEl = document.getElementById('courier-loading');
    const listEl = document.getElementById('couriers-list');
    const noCouriersEl = document.getElementById('no-couriers');

    loadingEl.style.display = 'flex';
    listEl.style.display = 'none';
    noCouriersEl.style.display = 'none';

    try {
        const res = await fetch("{{ route('checkout.getCouriers') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                pincode: pincode,
                payment_method: paymentMethod
            })
        });

        const data = await res.json();
        loadingEl.style.display = 'none';

        if (data.status === 'success' && data.couriers && data.couriers.length > 0) {
            listEl.innerHTML = '';
            listEl.style.display = 'flex';

            data.couriers.forEach((courier, index) => {
                const isChecked = index === 0 ? 'checked' : '';
                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.innerHTML = `
                    <label class="border border-gray-100 rounded-10 p-16 d-flex align-items-center justify-content-between gap-12 cursor-pointer courier-option-card ${index === 0 ? 'courier-card-active' : ''}" style="${index === 0 ? 'border: 2px solid #10b981;' : 'border: 1px solid #e5e7eb;'}">
                        <div class="d-flex align-items-center gap-10">
                            <input type="radio" name="selected_courier_radio" value="${courier.id}" data-name="${courier.name}" data-rate="${courier.rate}" class="form-check-input mt-0 courier-radio" ${isChecked} onchange="onCourierSelected(this)">
                            <div>
                                <span class="d-block fw-semibold text-gray-900 text-sm">${courier.name}</span>
                                <span class="text-xs text-gray-500"><i class="ph ph-clock me-2"></i> ETD: ${courier.etd}</span>
                            </div>
                        </div>
                        <span class="fw-bold text-main-600 text-sm">₹${parseFloat(courier.rate).toFixed(2)}</span>
                    </label>
                `;
                listEl.appendChild(col);
            });

            // Automatically apply the first (cheapest) courier
            selectCourier(data.couriers[0].id, data.couriers[0].name, data.couriers[0].rate);

        } else {
            noCouriersEl.style.display = 'block';
            selectCourier('', 'Standard Delivery', 0);
        }
    } catch (err) {
        loadingEl.style.display = 'none';
        noCouriersEl.style.display = 'block';
        selectCourier('', 'Standard Delivery', 0);
    }
}

function onCourierSelected(radio) {
    document.querySelectorAll('.courier-option-card').forEach(card => {
        card.style.border = '1px solid #e5e7eb';
        card.classList.remove('courier-card-active');
    });
    radio.closest('.courier-option-card').style.border = '2px solid #10b981';
    radio.closest('.courier-option-card').classList.add('courier-card-active');

    selectCourier(radio.value, radio.dataset.name, radio.dataset.rate);
}

function selectCourier(id, name, rate) {
    document.getElementById('hidden_courier_id').value = id || '';
    document.getElementById('hidden_courier_name').value = name || '';
    currentShippingCharge = parseFloat(rate || 0);
    updateTotals();
}

async function handlePlaceOrderClick() {
    const btn = document.getElementById('btn-place-order');
    const btnText = document.getElementById('btn-order-text');
    const spinner = document.getElementById('btn-order-spinner');

    const addressId = document.getElementById('hidden_address_id').value;
    if (!addressId) {
        showToast('Please add or select a delivery address.', 'error');
        return;
    }

    btn.disabled = true;
    spinner.style.display = 'inline-block';
    btnText.innerHTML = 'Processing...';

    const orderForm = document.getElementById('place-order-form');
    const formData = new FormData(orderForm);

    if (activePaymentMethod === 'Online Payment') {
        try {
            const prepRes = await fetch("{{ route('checkout.preparePayment') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    shipping_charge: document.getElementById('hidden_shipping_charge').value,
                    coupon_discount: document.getElementById('hidden_coupon_discount').value
                })
            });

            const prepData = await prepRes.json();

            if (prepRes.ok && prepData.status === 'success') {
                const options = {
                    key: prepData.razorpay_key,
                    amount: Math.round(prepData.amount * 100),
                    currency: "INR",
                    name: "PalVarnan",
                    description: "Order Checkout Payment",
                    order_id: prepData.razorpay_order_id,
                    handler: async function (response) {
                        formData.append('razorpay_payment_id', response.razorpay_payment_id);
                        formData.append('razorpay_order_id', response.razorpay_order_id);
                        formData.append('razorpay_signature', response.razorpay_signature);
                        await submitFinalOrder(formData);
                    },
                    prefill: {
                        name: prepData.user.name,
                        email: prepData.user.email,
                        contact: prepData.user.phone
                    },
                    theme: {
                        color: "#10b981"
                    },
                    modal: {
                        ondismiss: function () {
                            btn.disabled = false;
                            spinner.style.display = 'none';
                            btnText.innerHTML = '<i class="ph-bold ph-lock-key me-4"></i> Place Order';
                            showToast('Payment window closed.', 'error');
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();

            } else {
                showToast(prepData.message || 'Payment initiation failed.', 'error');
                btn.disabled = false;
                spinner.style.display = 'none';
                btnText.innerHTML = '<i class="ph-bold ph-lock-key me-4"></i> Place Order';
            }
        } catch (err) {
            showToast('Failed to initialize payment gateway.', 'error');
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.innerHTML = '<i class="ph-bold ph-lock-key me-4"></i> Place Order';
        }

    } else {
        // Cash on Delivery
        await submitFinalOrder(formData);
    }
}

async function submitFinalOrder(formData) {
    const btn = document.getElementById('btn-place-order');
    const btnText = document.getElementById('btn-order-text');
    const spinner = document.getElementById('btn-order-spinner');

    try {
        const res = await fetch("{{ route('checkout.placeOrder') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        });

        const data = await res.json();

        if (res.ok && data.status === 'success') {
            showToast(data.message || 'Order placed successfully!');
            setTimeout(() => {
                window.location.href = data.redirect_url || '/checkout/thankyou/' + data.order_number;
            }, 500);
        } else {
            showToast(data.message || 'Order placement failed.', 'error');
            btn.disabled = false;
            spinner.style.display = 'none';
            btnText.innerHTML = '<i class="ph-bold ph-lock-key me-4"></i> Place Order';
        }
    } catch (err) {
        showToast('Network error while placing order.', 'error');
        btn.disabled = false;
        spinner.style.display = 'none';
        btnText.innerHTML = '<i class="ph-bold ph-lock-key me-4"></i> Place Order';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (selectedPincode) {
        fetchCouriers(selectedPincode, activePaymentMethod);
    } else {
        const loadingEl = document.getElementById('courier-loading');
        if (loadingEl) {
            loadingEl.innerHTML = '<span class="text-gray-500 text-sm"><i class="ph ph-map-pin me-2"></i> Please add and save your delivery address above to see available courier delivery partners.</span>';
        }
    }
});
</script>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@endsection