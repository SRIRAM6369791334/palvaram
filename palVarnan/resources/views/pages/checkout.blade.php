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
                                        <button type="button" class="addr-edit-btn" onclick="toggleEdit({{ $addr->id }})">
                                            <i class="ph ph-pencil-simple"></i> Edit
                                        </button>
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

                <!-- <div class="mt-40">
                    <h6 class="text-lg mb-24">Additional Information</h6>
                    <input type="text" name="notes" class="common-input border-gray-100" placeholder="Notes about your order, e.g. special notes for delivery.">
                </div> -->
            </div>

            <div class="col-xl-3 col-lg-4">
                <div class="checkout-sidebar">
                    <div class="bg-color-three rounded-8 p-24 text-center">
                        <span class="text-gray-900 text-xl fw-semibold">Your Orders</span>
                    </div>

                    <div class="border border-gray-100 rounded-8 px-24 py-40 mt-24">
                        <div class="mb-32 pb-32 border-bottom border-gray-100 flex-between gap-8">
                            <span class="text-gray-900 fw-medium text-xl font-heading-two">Product</span>
                            <span class="text-gray-900 fw-medium text-xl font-heading-two">Subtotal</span>
                        </div>

                        @forelse ($cartItems as $item)
                            <div class="flex-between gap-24 mb-32">
                                <div class="flex-align gap-12">
                                    <span class="text-gray-900 fw-normal text-md font-heading-two w-144">{{ $item->product->name ?? 'Product' }}</span>
                                    <span><i class="ph-bold ph-x"></i></span>
                                    <span class="text-gray-900 fw-semibold text-md font-heading-two">{{ $item->product_quantity }}</span>
                                </div>
                                <span class="text-gray-900 fw-bold text-md font-heading-two">
                                    ₹{{ number_format(($item->variant->offer_price ?? 0) * $item->product_quantity, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500">Your cart is empty.</p>
                        @endforelse

                        <div class="border-top border-gray-100 pt-30 mt-30">
                            <div class="mb-32 flex-between gap-8">
                                <span class="text-gray-900 font-heading-two text-xl fw-semibold">Subtotal</span>
                                <span class="text-gray-900 font-heading-two text-md fw-bold">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="mb-0 flex-between gap-8">
                                <span class="text-gray-900 font-heading-two text-xl fw-semibold">Total</span>
                                <span class="text-gray-900 font-heading-two text-md fw-bold">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-32 pt-32 border-top border-gray-100">
                        <p class="text-gray-500">Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#" class="text-main-600 text-decoration-underline">privacy policy</a>.</p>
                    </div>

                    <form action="{{ route('checkout.placeOrder') }}" method="POST">
                        @csrf
                        <input type="hidden" name="address_id" value="{{ $selectedAddressId }}">
                        <button type="submit" class="btn btn-main mt-40 py-18 w-100 rounded-8 mt-56" {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
       
    </div>
</section>


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
    submitBtn.innerHTML = 'Saving...';

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    || form.querySelector('input[name="_token"]').value,
            },
            body: new FormData(form),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            showToast(data.message || 'Saved successfully.');
            setTimeout(() => window.location.reload(), 700);
        } else if (res.status === 422) {
            showFormErrors(form, data.errors || {});
            showToast('Please fix the highlighted fields.', 'error');
        } else {
            showToast(data.message || 'Something went wrong.', 'error');
        }
    } catch (err) {
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
</script>

@endsection