@extends('layouts.app')
@section('main')


<div class="breadcrumb mb-0 py-26 bg-color-one">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">{{ $product->product_name }}</h6>
            <ul class="flex-align gap-8 flex-wrap">
                <li class="text-sm">
                    <a href="/" class="text-main-600 flex-align gap-8"><i class="ph ph-house"></i> Home</a>
                </li>
                <li class="flex-align text-gray-500"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm">
                    <a href="/products?category={{ $product->category_id }}" class="text-main-600">
                        {{ $product->cate_name }}
                    </a>
                </li>
                <li class="flex-align text-gray-500"><i class="ph ph-caret-right"></i></li>
                <li class="text-sm text-neutral-600">{{ $product->product_name }}</li>
            </ul>
        </div>
    </div>
</div>

<section class="product-details py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="row gy-4">

                 
                    <div class="col-xl-6">
                        <div class="product-details__left">
                            <div class="product-details__thumb-slider border border-gray-100 rounded-16">
                                <div class="product-details__thumb flex-center h-100 p-24">
                                    <img src="{{ env('MAIN_URL') }}images/{{ $product->product_image }}"
                                         alt="{{ $product->product_name }}"
                                         class="w-auto" style="max-height:400px; object-fit:contain;">
                                </div>
                            </div>
                        </div>
                    </div>

                 
                    <div class="col-xl-6">
                        <div class="product-details__content">

                            <h5 class="mb-12">{{ $product->product_name }}</h5>

                         
                            <div class="flex-align gap-12 flex-wrap mb-16">
                                @if($product->cate_name)
                                    <span class="text-sm text-gray-500">
                                        Category: <a href="/products?category={{ $product->category_id }}" class="text-main-600">{{ $product->cate_name }}</a>
                                    </span>
                                @endif
                                @if($product->brand_name)
                                    <span class="text-sm text-gray-500">
                                        Brand: <span class="text-heading fw-medium">{{ $product->brand_name }}</span>
                                    </span>
                                @endif
                            </div>

                            <span class="mt-24 pt-24 text-gray-700 border-top border-gray-100 d-block"></span>

                            @if($product->product_description)
                                <p class="text-gray-700">{{ $product->product_description }}</p>
                            @endif

                           
                            @php
                                $variantsWithUnit = $product->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
                                $firstVariant = $variantsWithUnit->first() ?? $product->variants->first();
                            @endphp

                            @if($variantsWithUnit->count() > 0)
                            <div class="mt-24 mb-8">
                                <span class="text-gray-900 d-block mb-12 fw-medium">Select Size / Weight:</span>
                                <div class="flex-align flex-wrap gap-8" id="variant-selector">
                                    @foreach($variantsWithUnit as $v)
                                        <button type="button"
                                            class="variant-pill border rounded-pill px-16 py-8 text-sm fw-medium transition-1
                                                {{ $loop->first ? 'bg-main-600 text-white border-main-600' : 'bg-white text-gray-700 border-gray-200 hover-bg-main-50' }}"
                                            data-variant-id="{{ $v->id }}"
                                            data-offer="{{ $v->offer_price }}"
                                            data-mrp="{{ $v->mrp_price }}">
                                            {{ $v->value }}{{ $v->unit->short_name ?? '' }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                          
                            <div class="mt-24 flex-align flex-wrap gap-16">
                                <h4 class="mb-0" id="detail-offer-price">
                                    ₹{{ $firstVariant->offer_price ?? 0 }}
                                </h4>
                                @if($firstVariant && $firstVariant->mrp_price)
                                    <span class="text-md text-gray-400 text-decoration-line-through" id="detail-mrp-price">
                                        ₹{{ $firstVariant->mrp_price }}
                                    </span>
                                @endif
                            </div>

                            <span class="mt-24 pt-24 text-gray-700 border-top border-gray-100 d-block"></span>

                            

                            <span class="text-gray-900 d-block mb-8">Quantity:</span>
                            <div class="flex-between gap-16 flex-wrap">
                                <div class="flex-align flex-wrap gap-16">

                                    @php
                                        $inCart = in_array($firstVariant->id ?? 0, $cartProductIds);
                                        $cartItem = $cartItems->where('product_varient_id', $firstVariant->id ?? 0)->first();
                                    @endphp

                                    <div class="detail-cart-control"
                                        data-product-id="{{ $product->id }}"
                                        data-variant-id="{{ $firstVariant->id ?? '' }}">

                                        @if($inCart)
                                            <div class="qty-box border border-gray-100 rounded-pill py-9 px-16 flex-align">
                                                <button type="button" class="quantity__minus p-4 text-gray-700 hover-text-main-600 flex-center"><i class="ph ph-minus"></i></button>
                                                <input type="number" class="quantity__input border-0 text-center w-32" value="{{ $cartItem->product_quantity ?? 1 }}" min="1" readonly>
                                                <button type="button" class="quantity__plus p-4 text-gray-700 hover-text-main-600 flex-center"><i class="ph ph-plus"></i></button>
                                            </div>
                                        @else
                                            <button type="button"
                                                class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-48">
                                                <i class="ph ph-shopping-cart"></i> Add To Cart
                                            </button>
                                        @endif

                                    </div>
                                </div>

                               
                                @auth
                                <div class="flex-align gap-12">
                                    <button type="button"
                                        class="wishlist-btn w-52 h-52 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle border-0"
                                        data-product-id="{{ $product->id }}"
                                        data-variant-id="{{ $firstVariant->id ?? '' }}">
                                        <i class="{{ $wishlistVariantIds->contains($firstVariant->id ?? 0) ? 'ph-fill' : 'ph' }} ph-heart"></i>
                                    </button>
                                </div>
                                @endauth

                            </div>

                            <span class="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block"></span>

                        </div>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="pt-80">
            <div class="product-dContent border rounded-24">
                <div class="product-dContent__header border-bottom border-gray-100 flex-between flex-wrap gap-16">
                    <ul class="nav common-tab nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-description" type="button">Description</button>
                        </li>
                        @if($product->product_specification)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-specification" type="button">Specifications</button>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="product-dContent__box">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pills-description">
                            <div class="mb-40">
                                <h6 class="mb-24">Product Description</h6>
                                <p class="text-gray-700">
                                    {{ $product->product_description ?? 'No description available.' }}
                                </p>
                            </div>
                        </div>
                        @if($product->product_specification)
                        <div class="tab-pane fade" id="pills-specification">
                            <div class="mb-40">
                                <h6 class="mb-24">Product Specifications</h6>
                                <p class="text-gray-700">{{ $product->product_specification }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        
        @if($relatedProducts->count() > 0)
        <div class="pt-80">
            <div class="section-heading">
                <h5 class="mb-0">You Might Also Like</h5>
            </div>
            <div class="row g-4 mt-24">
                @foreach($relatedProducts as $rp)

                    @php
                        $rv = $rp->variants->first();
                        $rpVariantsWithUnit = $rp->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
                    @endphp

                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">

                            @auth
                            <button type="button"
                                class="wishlist-btn-related position-absolute"
                                data-product-id="{{ $rp->id }}"
                                data-variant-id="{{ $rv->id ?? '' }}"
                                style="top:12px; right:12px; z-index:2; background:none; border:none; padding:0; cursor:pointer;">
                                <i class="ph{{ in_array($rp->id, $wishlistProductIds ?? []) ? '-fill' : '' }} ph-heart text-2xl {{ in_array($rp->id, $wishlistProductIds ?? []) ? 'text-danger-600' : 'text-gray-400' }} hover-text-danger-600"></i>
                            </button>
                            @endauth

                            <a href="/product/{{ $rp->slug }}"
                                class="product-card__thumb flex-center rounded-8 bg-gray-50 position-relative">
                                <img src="{{ env('MAIN_URL') }}images/{{ $rp->product_image }}"
                                    alt="{{ $rp->product_name }}"
                                    class="w-auto max-w-unset" style="max-height:180px; object-fit:contain;">
                            </a>

                            <div class="product-card__content mt-16">
                                <h6 class="title text-lg fw-semibold mt-12 mb-8">
                                    <a href="/product/{{ $rp->slug }}" class="link text-line-2">
                                        {{ $rp->product_name }}
                                    </a>
                                </h6>

                                <span class="text-sm text-gray-500">
                                    {{ $rp->cate_name }}
                                    @if($rp->subcate_name) / {{ $rp->subcate_name }} @endif
                                </span>

                                {{-- Variant Dropdown --}}
                                @if($rpVariantsWithUnit->count() > 0)
                                <div class="variant-dropdown-wrap mb-12 mt-12">
                                    <div class="custom-variant-select" data-product-id="{{ $rp->id }}">
                                        <div class="variant-selected-display">
                                            @php $rpFirst = $rpVariantsWithUnit->first(); @endphp
                                            <span class="variant-label">{{ $rpFirst->value }}{{ $rpFirst->unit->short_name ?? '' }}</span>
                                            <span class="variant-price-tag">₹{{ $rpFirst->offer_price }}</span>
                                            <i class="ph ph-caret-down variant-arrow"></i>
                                        </div>
                                        <ul class="variant-options-list">
                                            @foreach($rpVariantsWithUnit as $v)
                                            <li class="variant-option {{ $loop->first ? 'selected' : '' }}"
                                                data-variant-id="{{ $v->id }}"
                                                data-offer="{{ $v->offer_price }}"
                                                data-mrp="{{ $v->mrp_price }}">
                                                <span class="opt-size">{{ $v->value }}{{ $v->unit->short_name ?? '' }}</span>
                                                <span class="opt-price">
                                                    @if($v->mrp_price)
                                                        <span class="opt-mrp">₹{{ $v->mrp_price }}</span>
                                                    @endif
                                                    <span class="opt-offer">₹{{ $v->offer_price }}</span>
                                                </span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif

                                {{-- Price --}}
                                <div class="product-card__price my-20">
                                    @if($rv && $rv->mrp_price)
                                        <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹{{ $rv->mrp_price }}</span>
                                    @endif
                                    <span class="text-heading text-md fw-semibold">₹{{ $rv->offer_price ?? 0 }}</span>
                                </div>

                                @php
                                    $rpInCart = in_array($rv->id ?? 0, $cartProductIds);
                                    $rpCartItem = $cartItems->where('product_varient_id', $rv->id ?? 0)->first();
                                @endphp
                                <div class="cart-control" data-product-id="{{ $rp->id }}" data-variant-id="{{ $rv->id ?? '' }}">
                                    @if($rpInCart)
                                        <div class="qty-box d-flex rounded-8 overflow-hidden border border-gray-100">
                                            <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">−</button>
                                            <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="{{ $rpCartItem->product_quantity ?? 1 }}" min="1" readonly>
                                            <button type="button" class="quantity__plus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">+</button>
                                        </div>
                                    @else
                                        <button type="button"
                                            class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100">
                                            Add To Cart <i class="ph ph-shopping-cart"></i>
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

<script>
function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please login to add products to cart',
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Login Now'
    }).then(r => { if (r.isConfirmed) window.location.href = "/login"; });
}


function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity;
    });
}


function attachDetailAddToCart() {
    const control = document.querySelector('.detail-cart-control');
    if (!control) return;
    const btn = control.querySelector('.add-to-cart-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const productId = control.dataset.productId;
        const variantId = control.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            control.innerHTML = `
                <div class="qty-box border border-gray-100 rounded-pill py-9 px-16 flex-align">
                    <button type="button" class="quantity__minus p-4 text-gray-700 hover-text-main-600 flex-center"><i class="ph ph-minus"></i></button>
                    <input type="number" class="quantity__input border-0 text-center w-32" value="1" min="1" readonly>
                    <button type="button" class="quantity__plus p-4 text-gray-700 hover-text-main-600 flex-center"><i class="ph ph-plus"></i></button>
                </div>`;
            attachDetailQtyEvents();
            updateCartCount(1);
            Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });
    });
}

function attachDetailQtyEvents() {
    const control   = document.querySelector('.detail-cart-control');
    if (!control) return;
    const variantId = control.dataset.variantId;
    const input     = control.querySelector('.quantity__input');

    control.querySelector('.quantity__plus').addEventListener('click', function () {
        syncCart(variantId, parseInt(input.value) + 1, input);
    });

    control.querySelector('.quantity__minus').addEventListener('click', function () {
        const qty = parseInt(input.value);
        if (qty <= 1) {
            fetch("{{ route('cart.ajaxRemove') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ product_varient_id: variantId })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    control.innerHTML = `
                        <button type="button" class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-48">
                            <i class="ph ph-shopping-cart"></i> Add To Cart
                        </button>`;
                    attachDetailAddToCart();
                }
            });
        } else {
            syncCart(variantId, qty - 1, input);
        }
    });
}


function showQtyBox(controlDiv, qty) {
    controlDiv.innerHTML = `
        <div class="qty-box d-flex rounded-8 overflow-hidden border border-gray-100">
            <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">−</button>
            <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="${qty}" min="1" readonly>
            <button type="button" class="quantity__plus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">+</button>
        </div>`;
    attachCardQtyEvents(controlDiv);
}

function attachAddToCartEvent(controlDiv) {
    const btn = controlDiv.querySelector('.add-to-cart-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
        const productId = controlDiv.dataset.productId;
        const variantId = controlDiv.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        })
        .then(r => r.json())
        .then(data => {
            showQtyBox(controlDiv, 1);
            updateCartCount(1);
            Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
        });
    });
}

function attachCardQtyEvents(controlDiv) {
    const variantId = controlDiv.dataset.variantId;
    const input     = controlDiv.querySelector('.quantity__input');

    controlDiv.querySelector('.quantity__plus').addEventListener('click', function () {
        syncCart(variantId, parseInt(input.value) + 1, input);
    });

    controlDiv.querySelector('.quantity__minus').addEventListener('click', function () {
        const qty = parseInt(input.value);
        if (qty <= 1) {
            fetch("{{ route('cart.ajaxRemove') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ product_varient_id: variantId })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    controlDiv.innerHTML = `
                        <button type="button"
                            class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100">
                            Add To Cart <i class="ph ph-shopping-cart"></i>
                        </button>`;
                    attachAddToCartEvent(controlDiv);
                }
            });
        } else {
            syncCart(variantId, qty - 1, input);
        }
    });
}

// Init all .cart-control (related products)
document.querySelectorAll('.cart-control').forEach(controlDiv => {
    if (controlDiv.querySelector('.qty-box')) {
        attachCardQtyEvents(controlDiv);
    } else {
        attachAddToCartEvent(controlDiv);
    }
});


function updateCartCount(delta) {
    const el = document.getElementById('cart-count');
    if (el) el.textContent = parseInt(el.textContent || 0) + delta;
}


document.querySelectorAll('.variant-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
        const variantId  = this.dataset.variantId;
        const offerPrice = this.dataset.offer;
        const mrpPrice   = this.dataset.mrp;

        document.querySelectorAll('.variant-pill').forEach(p => {
            p.classList.remove('bg-main-600', 'text-white', 'border-main-600');
            p.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
        });
        this.classList.add('bg-main-600', 'text-white', 'border-main-600');
        this.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');

        document.getElementById('detail-offer-price').textContent = '₹' + offerPrice;
        const mrpEl = document.getElementById('detail-mrp-price');
        if (mrpEl) mrpEl.textContent = mrpPrice ? '₹' + mrpPrice : '';

        const control = document.querySelector('.detail-cart-control');
        if (control) {
            control.dataset.variantId = variantId;
            if (control.querySelector('.qty-box')) {
                control.innerHTML = `
                    <button type="button" class="add-to-cart-btn btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-48">
                        <i class="ph ph-shopping-cart"></i> Add To Cart
                    </button>`;
                attachDetailAddToCart();
            }
        }

        const wishlistBtn = document.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.dataset.variantId = variantId;
            const wIcon = wishlistBtn.querySelector('i');
            wIcon.className = 'ph ph-heart';
            fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(data => {
                wIcon.className = data.in_wishlist ? 'ph-fill ph-heart' : 'ph ph-heart';
            });
        }
    });
});


function initVariantDropdowns() {
    document.querySelectorAll('.custom-variant-select').forEach(function (select) {
        if (select.dataset.initialized) return;
        select.dataset.initialized = 'true';

        const display = select.querySelector('.variant-selected-display');
        const options = select.querySelectorAll('.variant-option');

        display.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.custom-variant-select.open').forEach(s => {
                if (s !== select) s.classList.remove('open');
            });
            select.classList.toggle('open');
        });

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                const variantId  = this.dataset.variantId;
                const offerPrice = this.dataset.offer;
                const mrpPrice   = this.dataset.mrp;
                const sizeText   = this.querySelector('.opt-size').textContent;

                select.querySelector('.variant-label').textContent = sizeText;
                select.querySelector('.variant-price-tag').textContent = '₹' + offerPrice;
                options.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                select.classList.remove('open');

                const card = select.closest('.product-card__content');
                card.querySelector('.product-card__price').innerHTML = `
                    ${mrpPrice ? `<span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹${mrpPrice}</span>` : ''}
                    <span class="text-heading text-md fw-semibold">₹${offerPrice}</span>
                `;

                const wishlistBtn = select.closest('.product-card').querySelector('.wishlist-btn-related');
                if (wishlistBtn) {
                    wishlistBtn.dataset.variantId = variantId;
                    const wIcon = wishlistBtn.querySelector('i');
                    wIcon.className = 'ph ph-heart text-gray-400 text-2xl';
                    fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        wIcon.className = data.in_wishlist
                            ? 'ph-fill ph-heart text-danger-600 text-2xl'
                            : 'ph ph-heart text-gray-400 text-2xl';
                    });
                }

                const control = card.querySelector('.cart-control');
                if (control) {
                    control.dataset.variantId = variantId;
                    if (control.querySelector('.qty-box')) {
                        control.innerHTML = `
                            <button type="button"
                                class="add-to-cart-btn product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100">
                                Add To Cart <i class="ph ph-shopping-cart"></i>
                            </button>`;
                        attachAddToCartEvent(control);
                    }
                }
            });
        });
    });
}

document.addEventListener('click', function () {
    document.querySelectorAll('.custom-variant-select.open').forEach(s => s.classList.remove('open'));
});

initVariantDropdowns();


const detailWishlistBtn = document.querySelector('.wishlist-btn');
if (detailWishlistBtn) {
    detailWishlistBtn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        if (icon.classList.contains('ph-fill')) {
            doWishlistToggle(self, productId, variantId, icon);
            return;
        }

        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                Swal.fire({
                    icon: 'question',
                    title: 'Already in Wishlist',
                    html: `You already have <strong>${data.existing_variant}</strong> in your wishlist.<br>Do you want to add <strong>${data.new_variant}</strong> also?`,
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Add it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) doWishlistToggle(self, productId, variantId, icon);
                });
            } else {
                doWishlistToggle(self, productId, variantId, icon);
            }
        });
    });
}


document.querySelectorAll('.wishlist-btn-related').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        if (icon.classList.contains('ph-fill')) {
            doWishlistToggle(self, productId, variantId, icon);
            return;
        }

        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                Swal.fire({
                    icon: 'question',
                    title: 'Already in Wishlist',
                    html: `You already have <strong>${data.existing_variant}</strong> in your wishlist.<br>Do you want to add <strong>${data.new_variant}</strong> also?`,
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Add it!',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) doWishlistToggle(self, productId, variantId, icon);
                });
            } else {
                doWishlistToggle(self, productId, variantId, icon);
            }
        });
    });
});


function doWishlistToggle(btn, productId, variantId, icon) {
    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_id: productId, variant_id: variantId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'added') {
            icon.classList.remove('ph-heart', 'text-gray-400');
            icon.classList.add('ph-fill', 'ph-heart', 'text-danger-600');
        } else {
            icon.classList.remove('ph-fill', 'text-danger-600');
            icon.classList.add('ph-heart', 'text-gray-400');
        }
        Swal.fire({
            icon: data.status === 'added' ? 'success' : 'info',
            title: data.status === 'added' ? 'Added to Wishlist!' : 'Removed from Wishlist',
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1800, timerProgressBar: true
        });
    });
}


attachDetailAddToCart();
@if($cartItems->where('product_varient_id', $firstVariant->id ?? 0)->first())
    attachDetailQtyEvents();
@endif
</script>

@endsection