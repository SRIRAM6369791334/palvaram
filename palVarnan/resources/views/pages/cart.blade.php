@extends('layouts.app')
@section('main')


    <!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container container-lg">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Cart</h6>
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
                <li class="text-sm text-main-600">  Cart </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

    <!-- ================================ Cart Section Start ================================ -->
 <section class="cart py-80">
        <div class="container container-lg">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-24 rounded-8" role="alert">
                <i class="ph ph-warning-circle me-8 text-lg align-middle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-24 rounded-8" role="alert">
                <i class="ph ph-check-circle me-8 text-lg align-middle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row gy-4">
            <div class="col-xl-9 col-lg-8">
                <div class="cart-table border border-gray-100 rounded-8 px-40 py-48">
                    <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                    <form method="POST" action="{{ route('cart.update') }}">
                        @csrf
                        <table class="table style-three">
                            <thead>
                                <tr>
                                    <th class="h6 mb-0 text-lg fw-bold">Delete</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Product Name</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Price</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Quantity</th>
                                    <th class="h6 mb-0 text-lg fw-bold">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($cartItems as $item)

                                <tr data-variant-id="{{ $item->product_varient_id }}" data-cart-id="{{ $item->id }}">

                                    <td>
                                        <a href="javascript:void(0)"
                                            data-url="{{ route('cart.remove', $item->id) }}"
                                            class="remove-cart-item remove-tr-btn flex-align gap-12 hover-text-danger-600">

                                            <i class="ph ph-x-circle text-2xl d-flex"></i>

                                        </a>
                                    </td>

                                    <td>
                                        <div class="table-product d-flex align-items-center gap-12">

                                            <a href="/product/{{ $item->product->slug }}"
                                            class="table-product__thumb border border-gray-100 rounded-8 flex-center">

                                                <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}"
                                                alt="">

                                            </a>

                                            <div class="table-product__content text-start">
                                                <h6 class="title text-lg fw-semibold mb-4">
                                                    <a href="/product/{{ $item->product->slug }}" class="link text-line-2">
                                                        {{ $item->product->product_name }}
                                                    </a>
                                                </h6>

                                              
                                                @if($item->variant && !empty($item->variant->value) && $item->variant->unit)
                                                    <span class="text-xs fw-semibold text-main-600 bg-main-50 border border-main-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                        {{ $item->variant->value }}{{ $item->variant->unit->short_name ?? '' }}
                                                    </span>
                                                @elseif($item->variant && ($item->variant->size || $item->variant->color || $item->variant->varient_name))
                                                    @php
                                                        $giftParts = [];
                                                        if ($item->variant->size) $giftParts[] = 'Size: ' . $item->variant->size->size;
                                                        if ($item->variant->color) $giftParts[] = 'Color: ' . $item->variant->color->color;
                                                        $giftLabel = !empty($giftParts) ? implode(' | ', $giftParts) : ($item->variant->varient_name ?: 'Gift Item');
                                                    @endphp
                                                    <span class="text-xs fw-semibold text-purple-700 bg-purple-50 border border-purple-200 rounded-pill px-10 py-2 d-inline-block mt-4">
                                                        <i class="ph-bold ph-gift me-1"></i>{{ $giftLabel }}
                                                    </span>
                                                @endif

                                            </div>

                                        </div>
                                    </td>

                                    <td>
                                        <span class="text-lg h6 mb-0 fw-semibold">
                                            {{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="d-flex rounded-4 overflow-hidden quantity-box">

                                            <button type="button"
                                                class="quantity__minus border border-end border-gray-100 flex-shrink-0 h-36 w-36 text-neutral-600 flex-center hover-bg-main-600 hover-text-white">

                                                <i class="ph ph-minus"></i>

                                            </button>

                                            <input type="number"
                                                name="quantities[{{ $item->id }}]"
                                                class="quantity__input flex-grow-1 border border-gray-100 border-start-0 border-end-0 text-center w-24 px-2"
                                                value="{{ $item->product_quantity }}"
                                                min="1">

                                            <button type="button"
                                                class="quantity__plus border border-start border-gray-100 flex-shrink-0 h-36 w-36 text-neutral-600 flex-center hover-bg-main-600 hover-text-white">

                                                <i class="ph ph-plus"></i>

                                            </button>

                                        </div>
                                    </td>

                                    <td>

                                        <span class="text-lg h6 mb-0 fw-semibold">

                                            {{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price * $item->product_quantity }}

                                        </span>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Cart is empty
                                    </td>
                                </tr>

                                @endforelse

                                </tbody>
                        </table>
                    </form>
                    </div>


                    <div class="flex-between flex-wrap gap-16 mt-16">
                        <div class="flex-align gap-16">
                            <input type="text" id="coupon-code-input" class="common-input" placeholder="Coupon Code">
                            <button type="button" id="btn-apply-coupon" class="btn btn-main py-18 w-100 rounded-8">Apply Coupon</button>
                        </div>

                        <a href="/products" class="btn btn-main py-18 px-32 rounded-8">Continue Shopping</a>
                    </div>

                </div>
            </div>
            <div class="col-xl-3 col-lg-4">
                <div class="cart-sidebar border border-gray-100 rounded-8 px-24 py-40">
                    <h6 class="text-xl mb-32">Cart Totals</h6>
                                        @php
                        $delivery = $subtotal > 0 ? 50.00 : 0.00;
                        $tax = round($subtotal * 0.16, 2);
                        $grandTotal = $subtotal + $delivery + $tax;
                    @endphp
                    <div class="bg-color-three rounded-8 p-24">
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Subtotal</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-subtotal">
                                {{ $currencySymbol ?? "₹" }}{{ number_format($subtotal, 2) }}
                            </span>
                        </div>
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Estimated Delivery</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-delivery">
                                {{ $currencySymbol ?? "₹" }}{{ number_format($delivery, 2) }}
                            </span>
                        </div>
                        <div class="mb-0 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Estimated Taxes (16%)</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-tax">
                                {{ $currencySymbol ?? "₹" }}{{ number_format($tax, 2) }}
                            </span>
                        </div>
                    </div>  
                    <div class="bg-color-three rounded-8 p-24 mt-24">
                        <div class="flex-between gap-8">
                            <span class="text-gray-900 text-xl fw-semibold">Total</span>
                            <span class="text-gray-900 text-xl fw-bold text-main-600" id="cart-sidebar-total">
                                {{ $currencySymbol ?? "₹" }}{{ number_format($grandTotal, 2) }}
                            </span>
                        </div>
                    </div>  
                    @auth
                        @if(count($cartItems) > 0)
                            <a href="/checkout" class="btn btn-main mt-40 py-18 w-100 rounded-8">
                                Proceed to checkout
                            </a>
                        @else
                            <button type="button" disabled class="btn btn-main mt-40 py-18 w-100 rounded-8 opacity-50" style="cursor: not-allowed; opacity: 0.5;">
                                Proceed to checkout
                            </button>
                        @endif
                    @else
                        @if(count($cartItems) > 0)
                            <a href="javascript:void(0)" onclick="showLoginAlert()"
                                class="btn btn-main mt-40 py-18 w-100 rounded-8">
                                Proceed to checkout
                            </a>
                        @else
                            <button type="button" disabled class="btn btn-main mt-40 py-18 w-100 rounded-8 opacity-50" style="cursor: not-allowed; opacity: 0.5;">
                                Proceed to checkout
                            </button>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
 </section>
<!-- ================================ Cart Section End ================================ -->

<script>
let syncTimeout = null;
function debouncedSyncQuantity(variantId, qty, inputEl, subtotalCellEl, unitPrice, rowEl) {
    clearTimeout(syncTimeout);
    syncTimeout = setTimeout(() => {
        fetch('/cart/ajax-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_varient_id: variantId, quantity: qty })
        })
        .then(r => r.json())
        .then(data => {
            if (data.clamped) {
                if (inputEl) inputEl.value = data.quantity;
                const curr = '{{ $currencySymbol ?? "₹" }}';
                if (subtotalCellEl && unitPrice) {
                    subtotalCellEl.textContent = curr + (unitPrice * data.quantity).toFixed(2);
                }
                updateCartTotal();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limit Reached',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } else if (data.removed) {
                if (rowEl) rowEl.remove();
                updateCartTotal();
                if (document.querySelectorAll('.quantity-box').length === 0) {
                    window.location.reload();
                }
            }
            if (data.success) {
                fetch('/cart/subtotal')
                    .then(r => r.json())
                    .then(sub => {
                        const headerBadge = document.querySelector('.cart-count, [data-cart-count]');
                        if (headerBadge && sub.count) headerBadge.textContent = sub.count;
                    });
            }
        })
        .catch(err => console.error('Cart sync error:', err));
    }, 350);
}

document.querySelectorAll('.quantity-box').forEach(box => {
    const input = box.querySelector('.quantity__input');
    const row = box.closest('tr');
    const price = parseFloat(
        row.querySelector('td:nth-child(3) span').textContent.replace(/[^\d.]/g, '')
    ) || 0;
    const subtotalCell = row.querySelector('td:last-child span');

    function updateSubtotal() {
        let rawVal = parseInt(input.value);
        if (isNaN(rawVal) || rawVal < 1) {
            rawVal = 1;
            input.value = 1;
        }
        const curr = '{{ $currencySymbol ?? "₹" }}';
        subtotalCell.textContent = curr + (price * rawVal).toFixed(2);
        updateCartTotal();
        const variantId = row.dataset.variantId;
        if (variantId) {
            debouncedSyncQuantity(variantId, rawVal, input, subtotalCell, price, row);
        }
    }

    box.querySelector('.quantity__plus').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        input.value = parseInt(input.value) + 1;
        updateSubtotal();
    });

    box.querySelector('.quantity__minus').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateSubtotal();
        }
    });

    input.addEventListener('change', updateSubtotal);
});

// ── Recalculate sidebar subtotal ──────────────────────────────────────────────
function updateCartTotal() {
    let subtotal = 0;
    let hasItems = false;
    document.querySelectorAll('.quantity-box').forEach(box => {
        hasItems = true;
        const input = box.querySelector('.quantity__input');
        const price = parseFloat(
            box.closest('tr').querySelector('td:nth-child(3) span').textContent.replace(/[^\d.]/g, '')
        ) || 0;
        subtotal += price * (parseInt(input.value) || 1);
    });

    const delivery = (subtotal > 0 && hasItems) ? 50.00 : 0.00;
    const tax = roundToTwo(subtotal * 0.16);
    const discEl = document.getElementById('cart-sidebar-discount');
    const discount = discEl ? (parseFloat(discEl.textContent.replace(/[^\d.]/g, '')) || 0) : 0;
    const grandTotal = Math.max(0, roundToTwo((subtotal + delivery + tax) - discount));

    const subtotalEl = document.getElementById('cart-sidebar-subtotal');
    const curr = '{{ $currencySymbol ?? "₹" }}';
    if (subtotalEl) subtotalEl.textContent = curr + subtotal.toFixed(2);

    const deliveryEl = document.getElementById('cart-sidebar-delivery');
    if (deliveryEl) deliveryEl.textContent = '{{ $currencySymbol ?? "₹" }}' + delivery.toFixed(2);

    const taxEl = document.getElementById('cart-sidebar-tax');
    if (taxEl) taxEl.textContent = '{{ $currencySymbol ?? "₹" }}' + tax.toFixed(2);

    const totalEl = document.getElementById('cart-sidebar-total');
    if (totalEl) totalEl.textContent = '{{ $currencySymbol ?? "₹" }}' + grandTotal.toFixed(2);

    if (!hasItems) {
        const tbody = document.querySelector('.table.style-three tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-40 text-gray-500">Your cart is currently empty. <a href="/products" class="text-main-600 text-decoration-underline ms-2 fw-semibold">Continue Shopping</a></td></tr>';
        }
        const checkoutBtn = document.querySelector('.cart-sidebar .btn-main');
        if (checkoutBtn) {
            checkoutBtn.classList.add('disabled', 'opacity-50');
            checkoutBtn.setAttribute('disabled', 'disabled');
        }
    }
}

function roundToTwo(num) {
    return +(Math.round(num + "e+2")  + "e-2");
}

// ── Remove cart item ────────────────────────────────────────────────────────
document.querySelectorAll('.remove-cart-item').forEach(button => {
    button.addEventListener('click', function () {
        const url = this.dataset.url;
        const row = this.closest('tr');

        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                row.remove();
                updateCartTotal();
                Swal.fire({
                    icon: 'success',
                    title: 'Removed!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
});

function showLoginAlert() {
    Swal.fire({
        icon: 'info',
        title: 'Login to Proceed',
        text: 'Please login or sign up to complete your checkout. Your selected cart items will be saved.',
        confirmButtonColor: '#0c716a',
        confirmButtonText: 'Login to Checkout',
        showCancelButton: true,
        cancelButtonText: 'Continue Shopping'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/login?redirect=/checkout";
        }
    });
}

// ── Coupon Application Handler (TC-C11, TC-C12, TC-C13) ──────────────────────────
const applyCouponBtn = document.getElementById('btn-apply-coupon');
if (applyCouponBtn) {
    applyCouponBtn.addEventListener('click', function () {
        const curr = '{{ $currencySymbol ?? "₹" }}';
        const input = document.getElementById('coupon-code-input');
        const code = input ? input.value.trim() : '';
        if (!code) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Enter Coupon', text: 'Please enter a coupon code.', confirmButtonColor: '#28a745' });
            } else {
                alert('Please enter a coupon code.');
            }
            return;
        }

        fetch('/cart/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ coupon_code: code })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Coupon Applied!', text: data.message, confirmButtonColor: '#28a745' });
                }
                let discRow = document.getElementById('cart-sidebar-discount-row');
                if (!discRow) {
                    const subRow = document.getElementById('cart-sidebar-subtotal')?.parentElement;
                    if (subRow) {
                        discRow = document.createElement('div');
                        discRow.id = 'cart-sidebar-discount-row';
                        discRow.className = 'mb-32 flex-between gap-8 text-success-600';
                        discRow.innerHTML = `<span class="font-heading-two text-success-600">Coupon (${data.code})</span><span class="fw-semibold text-success-600" id="cart-sidebar-discount">-${curr}${parseFloat(data.discount).toFixed(2)}</span>`;
                        subRow.after(discRow);
                    }
                } else {
                    const lbl = discRow.querySelector('.font-heading-two');
                    if (lbl) lbl.textContent = `Coupon (${data.code})`;
                    document.getElementById('cart-sidebar-discount').textContent = '-' + curr + parseFloat(data.discount).toFixed(2);
                }
                updateCartTotal();
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Coupon Error', text: data.message, confirmButtonColor: '#d33' });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            console.error('Coupon error:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process coupon request.', confirmButtonColor: '#d33' });
            }
        });
    });
}

</script>

@endsection