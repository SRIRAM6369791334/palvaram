@extends('layouts.app')
@section('main')

    <!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">My Wishlist</h6>
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
                <li class="text-sm text-main-600"> Wishlist </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

    <!-- ================================ Wishlist Section Start ================================ -->
<section class="cart py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="cart-table border border-gray-100 rounded-8 px-40 py-48">
                    <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                        <table class="table style-three">
                            <thead>
                                <tr>
                                    <th class="h6 mb-0 text-lg fw-bold">Delete</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Product Name</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Unit Price</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Stock Status</th>
                                    <th class="h6 mb-0 text-lg fw-bold text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="wishlist-table-body">
                                @forelse($items as $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->variant ?? ($product ? $product->variants->first() : null);
                                        $offerPrice = $variant->offer_price ?? 0;
                                        $mrpPrice = $variant->mrp_price ?? 0;
                                        $productImg = Str::startsWith($product->product_image, 'http') ? $product->product_image : env('MAIN_URL') . 'images/' . $product->product_image;
                                    @endphp
                                    <tr id="wishlist-row-{{ $item->id }}">
                                        <td>
                                            <a href="javascript:void(0)"
                                                class="remove-wishlist-item remove-tr-btn flex-align gap-12 hover-text-danger-600"
                                                data-id="{{ $item->id }}">
                                                <i class="ph ph-x-circle text-2xl d-flex"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="table-product d-flex align-items-center gap-12">
                                                <a href="/product/{{ $product->slug }}"
                                                    class="table-product__thumb border border-gray-100 rounded-8 flex-center">
                                                    <img src="{{ $productImg }}" alt="{{ $product->product_name }}" onerror="this.src='/assets/images/thumbs/product-img1.png'">
                                                </a>
                                                <div class="table-product__content text-start">
                                                    <h6 class="title text-lg fw-semibold mb-4">
                                                        <a href="/product/{{ $product->slug }}" class="link text-line-2">
                                                            {{ $product->product_name }}
                                                        </a>
                                                    </h6>
                                                    @if($variant && !empty($variant->value) && $variant->unit)
                                                        <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                            {{ $variant->value }}{{ $variant->unit->short_name ?? '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-lg h6 mb-0 fw-semibold">
                                                ₹{{ number_format($offerPrice, 2) }}
                                            </span>
                                            @if($mrpPrice > $offerPrice)
                                                <span class="text-xs text-gray-400 text-decoration-line-through d-block mt-2">
                                                    ₹{{ number_format($mrpPrice, 2) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-15 fw-semibold text-success-600">
                                                In Stock
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @auth
                                            <button type="button"
                                                class="add-to-cart-from-wishlist btn btn-main py-10 px-24 rounded-8"
                                                data-id="{{ $item->id }}"
                                                data-product-id="{{ $product->id }}"
                                                data-variant-id="{{ $variant->id ?? '' }}">
                                                Add To Cart <i class="ph ph-shopping-cart ms-4"></i>
                                            </button>
                                            @else
                                            <button type="button"
                                                onclick="showLoginAlert()"
                                                class="btn btn-main py-10 px-24 rounded-8">
                                                Add To Cart <i class="ph ph-shopping-cart ms-4"></i>
                                            </button>
                                            @endauth
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="wishlist-empty-row">
                                        <td colspan="5" class="text-center py-40">
                                            <div class="py-24">
                                                <i class="ph ph-heart text-4xl text-gray-300 d-block mb-12"></i>
                                                <p class="text-gray-500 mb-0">Your wishlist is empty</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex-between flex-wrap gap-16 mt-24">
                        <a href="/products" class="btn btn-main py-18 px-32 rounded-8">Continue Shopping</a>
                        <a href="{{ url('/cart') }}" class="btn btn-outline-main py-18 px-32 rounded-8 border-main-600 text-main-600 hover-bg-main-600 hover-text-white">View Shopping Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================================ Wishlist Section End ================================ -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Remove item from Wishlist
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-wishlist-item');
        if (!btn) return;

        const id = btn.dataset.id;
        const row = document.getElementById(`wishlist-row-${id}`);

        fetch(`/wishlist/${id}/remove`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (row) row.remove();

                // Update header wishlist badge counter
                const headerBadge = document.getElementById('wishlist-count-badge');
                if (headerBadge) {
                    let current = parseInt(headerBadge.textContent.trim()) || 0;
                    headerBadge.textContent = Math.max(0, current - 1);
                }

                // Check if table is now empty
                const tableBody = document.getElementById('wishlist-table-body');
                if (tableBody && tableBody.querySelectorAll('tr[id^="wishlist-row-"]').length === 0) {
                    tableBody.innerHTML = `
                        <tr id="wishlist-empty-row">
                            <td colspan="5" class="text-center py-40">
                                <div class="py-24">
                                    <i class="ph ph-heart text-4xl text-gray-300 d-block mb-12"></i>
                                    <p class="text-gray-500 mb-0">Your wishlist is empty</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Removed from Wishlist',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true
                });
            }
        });
    });

    // Add to Cart from Wishlist
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.add-to-cart-from-wishlist');
        if (!btn) return;

        const productId = btn.dataset.productId;
        const variantId = btn.dataset.variantId;
        const origHTML  = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Adding...';

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify({
                product_id         : productId,
                product_varient_id : variantId,
                quantity           : 1
            })
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: data.message || 'Added to Cart!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            // Update header cart count and floating cart bar
            if (typeof updateCartCount === 'function') {
                updateCartCount(1);
            }

            // Change button state
            btn.innerHTML = '<i class="ph ph-check me-1"></i> Added to Cart';
            btn.classList.remove('btn-main');
            btn.classList.add('btn-success');
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = origHTML;
        });
    });

});

function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please log in to add items to your cart',
        confirmButtonColor: '#1c799b',
        confirmButtonText: 'Login Now'
    }).then(r => { 
        if (r.isConfirmed) window.location.href = "/login"; 
    });
}
</script>

@endsection