@extends('layouts.app')
@section('main')


    <!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Products</h6>
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
                <li class="text-sm text-main-600"> Products </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

    <!-- =============================== Shop Section Start ======================================== -->
 <section class="shop py-80">
    <div class="container container-lg">
        <div class="row">

            <!-- Sidebar Start -->
            <div class="col-lg-3">
                <div class="shop-sidebar">

                    <button type="button"
                        class="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600">
                        <i class="ph ph-x"></i>
                    </button>

                    <!-- CATEGORY BOX -->
                    <div class="shop-sidebar__box border border-gray-100 rounded-8 p-24 mb-32">

                        <h6 class="text-xl border-bottom border-gray-100 pb-20 mb-20">
                            Product Category
                        </h6>

                        <div class="category-sidebar">

                        @foreach($categories as $category)

                            @php
                                $subs = $subcategories->where('category_name', $category->id);
                            @endphp

                            <div class="category-item border-bottom py-12">

                                <div class="d-flex justify-content-between align-items-center category-toggle"
                                    data-target="cat{{ $category->id }}"
                                    style="cursor:pointer;">

                                    <a href="/products?category={{ $category->id }}"
                                    class="text-gray-900 fw-medium">
                                        {{ $category->category_name }}
                                    </a>

                                    @if($subs->count() > 0)
                                        <span class="toggle-icon">
                                            <i class="ph ph-plus"></i>
                                        </span>
                                    @endif

                                </div>

                                @if($subs->count() > 0)

                                    <ul id="cat{{ $category->id }}"
                                        class="subcategory-list d-none ps-3 mt-2">

                                        @foreach($subs as $sub)

                                            <li class="mb-2 mt-2">

                                                <a href="/products?subcategory={{ $sub->id }}"
                                                class="text-gray-900 fw-medium">

                                                    <i class="ph ph-caret-right"></i>
                                                    {{ $sub->subcategory_name }}

                                                </a>

                                            </li>

                                        @endforeach

                                    </ul>

                                @endif

                            </div>

                        @endforeach

                        </div>

                    </div>

                    <div class="shop-sidebar__box border border-gray-100 rounded-8 p-24 mb-32">

                        <h6 class="text-xl border-bottom border-gray-100 pb-20 mb-20">
                            Brands
                        </h6>

                        <div class="category-sidebar">

                        @foreach($brands as $brand)
                            <div class="category-item border-bottom py-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="/products?brand={{ $brand->id }}"
                                        class="text-gray-900 fw-medium {{ request('brand') == $brand->id ? 'text-main-600 fw-bold' : '' }}">
                                        {{ $brand->brand_name }}
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        </div>

                    </div>

                    <!-- PRICE FILTER (UNCHANGED) -->
                    <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">

                        <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">
                            Filter by Price
                        </h6>

                        <div class="custom--range">

                            <div id="slider-range"></div>

                            <div class="flex-between flex-wrap-reverse gap-8 mt-24">

                                <button type="button" class="btn btn-main h-40 flex-align">
                                    Filter
                                </button>

                                <div class="custom--range__content flex-align gap-8">

                                    <span class="text-gray-500 text-md flex-shrink-0">
                                        Price:
                                    </span>

                                    <input type="text"
                                        class="custom--range__prices text-neutral-600 text-start text-md fw-medium"
                                        id="amount"
                                        readonly>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Content Start -->
            <div class="col-lg-9">


                <div class="flex-between gap-16 flex-wrap mb-40">

                    <span class="text-gray-900">
                        Showing {{ $products->firstItem() ?? 0 }}
                        -
                        {{ $products->lastItem() ?? 0 }}
                        of
                        {{ $products->total() }} results
                    </span>

                </div>


                <div class="row g-4">

                    @forelse($products as $product)

                        <div class="col-lg-4 col-md-6">

                            <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">

                                @auth
                                <button type="button"
                                    class="wishlist-btn position-absolute"
                                    data-product-id="{{ $product->id }}"
                                    data-variant-id="{{ $product->variants->first()->id ?? '' }}"
                                    style="top:12px; right:12px; z-index:2; background:none; border:none; padding:0; cursor:pointer;">
                                    <i class="ph{{ in_array($product->id, $wishlistProductIds ?? []) ? '-fill' : '' }} ph-heart text-2xl {{ in_array($product->id, $wishlistProductIds ?? []) ? 'text-danger-600' : 'text-gray-400' }} hover-text-danger-600"></i>
                                </button>
                                @endauth

                                <a href="/product/{{ $product->slug }}"
                                class="product-card__thumb flex-center rounded-8 bg-gray-50 position-relative">

                                    <img src="{{ env('MAIN_URL') }}images/{{ $product->product_image }}"
                                        alt="{{ $product->product_name }}"
                                        class="w-auto max-w-unset">
                                </a>

                                <div class="product-card__content mt-16">

                                    <h6 class="title text-lg fw-semibold mt-12 mb-8">

                                        <a href="/product/{{ $product->slug }}"
                                        class="link text-line-2">

                                            {{ $product->product_name }}

                                        </a>

                                    </h6>

                                    <span class="text-sm text-gray-500">
                                        {{ $product->cate_name }}
                                        @if($product->subcate_name)
                                            / {{ $product->subcate_name }}
                                        @endif
                                    </span>

                                    @php
                                        $variant = $product->variants->first();
                                        $variantsWithUnit = $product->variants->filter(fn($v) => !empty($v->value) && !empty($v->unit));
                                    @endphp

                                    @if($variantsWithUnit->count() > 0)
                                    <div class="variant-dropdown-wrap mb-12 mt-12">
                                        <div class="custom-variant-select" data-product-id="{{ $product->id }}">
                                            <div class="variant-selected-display">
                                                @php $first = $variantsWithUnit->first(); @endphp
                                                <span class="variant-label">{{ $first->value }}{{ $first->unit->short_name ?? '' }}</span>
                                                <span class="variant-price-tag">₹{{ $first->offer_price }}</span>
                                                <i class="ph ph-caret-down variant-arrow"></i>
                                            </div>
                                            <ul class="variant-options-list">
                                                @foreach($variantsWithUnit as $v)
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

                                    <div class="product-card__price my-20">
                                        @if($variant && $variant->mrp_price)
                                            <span class="text-gray-400 text-md fw-semibold text-decoration-line-through">
                                                ₹{{ $variant->mrp_price }}
                                            </span>
                                        @endif
                                        <span class="text-heading text-md fw-semibold">
                                            ₹{{ $variant->offer_price ?? 0 }}
                                        </span>
                                    </div>

                                    @php
                                        $inCart   = in_array($variant->id, $cartProductIds);
                                        $cartItem = $cartItems->where('product_varient_id', $variant->id)->first();
                                    @endphp

                                    <div class="cart-control" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                                        @if($inCart)
                                            <div class="qty-box d-flex rounded-8 overflow-hidden border border-gray-100">
                                                <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">−</button>
                                                <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="{{ $cartItem->product_quantity ?? 1 }}" min="1" readonly>
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

                    @empty

                        <div class="col-12">

                            <div class="alert alert-warning">

                                No products found.

                            </div>

                        </div>

                    @endforelse

                </div>

                <div class="mt-40">

                    {{ $products->withQueryString()->links() }}

                </div>

            </div>
            <!-- Content End -->

        </div>
    </div>
 </section>
<!-- =============================== Shop Section End ======================================== -->

    <!-- ========================== Shipping Section Start ============================ -->
 <section class="shipping mb-24" id="shipping">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="400">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-car-profile"></i></span>
                    <div class="">
                        <h6 class="mb-0">Free Shipping</h6>
                        <span class="text-sm text-heading">Free shipping all over the US</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="600">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-hand-heart"></i></span>
                    <div class="">
                        <h6 class="mb-0"> 100% Satisfaction</h6>
                        <span class="text-sm text-heading">Free shipping all over the US</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="800">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-credit-card"></i></span>
                    <div class="">
                        <h6 class="mb-0"> Secure Payments</h6>
                        <span class="text-sm text-heading">Free shipping all over the US</span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6" data-aos="zoom-in" data-aos-duration="1000">
                <div class="shipping-item flex-align gap-16 rounded-16 bg-main-50 hover-bg-main-100 transition-2">
                    <span class="w-56 h-56 flex-center rounded-circle bg-main-600 text-white text-32 flex-shrink-0"><i class="ph-fill ph-chats"></i></span>
                    <div class="">
                        <h6 class="mb-0"> 24/7 Support</h6>
                        <span class="text-sm text-heading">Free shipping all over the US</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </section>
<!-- ========================== Shipping Section End ============================ -->
  
<script>
document.addEventListener("click", function (e) {

    const toggle = e.target.closest(".category-toggle");
    if (!toggle) return;

    const target = toggle.getAttribute("data-target");
    const el = document.getElementById(target);

    if (!el) return;

    el.classList.toggle("d-none");

    const icon = toggle.querySelector("i");

    if (icon.classList.contains("ph-plus")) {
        icon.classList.remove("ph-plus");
        icon.classList.add("ph-minus");
    } else {
        icon.classList.remove("ph-minus");
        icon.classList.add("ph-plus");
    }

});
</script>

<script>
function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please login to add products to cart',
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Login Now'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = "/login";
    });
}

function showQtyBox(controlDiv, qty) {
    controlDiv.innerHTML = `
        <div class="qty-box d-flex rounded-8 overflow-hidden border border-gray-100">
            <button type="button" class="quantity__minus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">−</button>
            <input type="number" class="quantity__input flex-grow-1 border-0 text-center fw-semibold w-24 px-2" value="${qty}" min="1" readonly>
            <button type="button" class="quantity__plus h-36 w-36 flex-center bg-gray-50 hover-bg-main-600 hover-text-white text-lg fw-bold">+</button>
        </div>`;
    attachQtyEvents(controlDiv);
}

function attachQtyEvents(controlDiv) {
    const variantId = controlDiv.dataset.variantId;
    const input = controlDiv.querySelector('.quantity__input');

    controlDiv.querySelector('.quantity__plus').addEventListener('click', function () {
        const newQty = parseInt(input.value) + 1;
        syncCart(variantId, newQty, input);
    });

    controlDiv.querySelector('.quantity__minus').addEventListener('click', function () {
        const currentQty = parseInt(input.value);

        if (currentQty <= 1) {
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
            syncCart(variantId, currentQty - 1, input);
        }
    });
}

function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity; // set from what we sent, already confirmed by DB
    });
}

function attachAddToCartEvent(controlDiv) {
    controlDiv.querySelector('.add-to-cart-btn').addEventListener('click', function () {
        const productId = controlDiv.dataset.productId;
        const variantId = controlDiv.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        }).then(r => r.json()).then(data => {
            showQtyBox(controlDiv, 1);
            Swal.fire({
                icon: 'success', title: data.message,
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2000, timerProgressBar: true
            });
        });
    });
}

// Init on page load
document.querySelectorAll('.cart-control').forEach(controlDiv => {
    if (controlDiv.querySelector('.qty-box')) {
        attachQtyEvents(controlDiv);
    } else {
        attachAddToCartEvent(controlDiv);
    }
});


// Custom variant dropdown
document.querySelectorAll('.custom-variant-select').forEach(function (select) {

    const display = select.querySelector('.variant-selected-display');
    const options = select.querySelectorAll('.variant-option');

    // Toggle open/close
    display.addEventListener('click', function (e) {
        e.stopPropagation();
        document.querySelectorAll('.custom-variant-select.open').forEach(s => {
            if (s !== select) s.classList.remove('open');
        });
        select.classList.toggle('open');
    });

    // Option click
    options.forEach(function (option) {
        option.addEventListener('click', function () {
            const variantId  = this.dataset.variantId;
            const offerPrice = this.dataset.offer;
            const mrpPrice   = this.dataset.mrp;
            const sizeText   = this.querySelector('.opt-size').textContent;

            // Update selected display
            select.querySelector('.variant-label').textContent = sizeText;
            select.querySelector('.variant-price-tag').textContent = '₹' + offerPrice;

            // Mark selected
            options.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');

            // Close dropdown
            select.classList.remove('open');

            // Update price in card
            const card = select.closest('.product-card__content');
            const priceBox = card.querySelector('.product-card__price');
            priceBox.innerHTML = `
                ${mrpPrice ? `<span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹${mrpPrice}</span>` : ''}
                <span class="text-heading text-md fw-semibold">₹${offerPrice}</span>
            `;

            const wishlistBtn = select.closest('.product-card').querySelector('.wishlist-btn');

            if (wishlistBtn) {
                wishlistBtn.dataset.variantId = variantId;

                const wIcon = wishlistBtn.querySelector('i');
                wIcon.className = 'ph ph-heart text-gray-400 text-2xl';

                fetch(`/wishlist/check?product_id=${wishlistBtn.dataset.productId}&variant_id=${variantId}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.in_wishlist) {
                        wIcon.className = 'ph-fill ph-heart text-danger-600 text-2xl';
                    } else {
                        wIcon.className = 'ph ph-heart text-gray-400 text-2xl';
                    }
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

document.addEventListener('click', function () {
    document.querySelectorAll('.custom-variant-select.open').forEach(s => {
        s.classList.remove('open');
    });
});

document.querySelectorAll('.wishlist-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
    
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const icon      = this.querySelector('i');
        const self      = this;

        // If already wishlisted (filled heart) → just remove, no need to check
        if (icon.classList.contains('ph-fill')) {
            toggleWishlist(self, productId, variantId, icon);
            return;
        }

        // Before adding, check if another variant of same product is already in wishlist
        fetch(`/wishlist/check-product?product_id=${productId}&exclude_variant_id=${variantId}`, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.has_other_variant) {
                // Another variant already in wishlist — ask user
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
                    if (result.isConfirmed) {
                        toggleWishlist(self, productId, variantId, icon);
                    }
                });
            } else {
                // No other variant in wishlist — add directly
                toggleWishlist(self, productId, variantId, icon);
            }
        });
    });
});

function toggleWishlist(btn, productId, variantId, icon) {
    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
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
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true
        });
    });
}

</script>

@endsection