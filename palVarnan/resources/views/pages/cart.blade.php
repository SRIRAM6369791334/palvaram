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

                                <tr>

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

                                            ₹{{ $item->variant->offer_price * $item->product_quantity }}

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
                            <input type="text" class="common-input" placeholder="Coupon Code">
                            <button type="button" class="btn btn-main py-18 w-100 rounded-8">Apply Coupon</button>
                        </div>

                        <a href="/products" class="btn btn-main py-18 px-32 rounded-8">Continue Shopping</a>
                    </div>

                </div>
            </div>
            <div class="col-xl-3 col-lg-4">
                <div class="cart-sidebar border border-gray-100 rounded-8 px-24 py-40">
                    <h6 class="text-xl mb-32">Cart Totals</h6>
                    <div class="bg-color-three rounded-8 p-24">
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Subtotal</span>

                            <span class="text-gray-900 fw-semibold">
                                ₹{{ number_format($subtotal, 2) }}
                            </span>
                        </div>
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Extimated Delivery</span>
                            <span class="text-gray-900 fw-semibold">₹50.00</span>
                        </div>
                        <div class="mb-0 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Extimated Taxs</span>
                            <span class="text-gray-900 fw-semibold">16%</span>
                        </div>
                    </div>  
                    <div class="bg-color-three rounded-8 p-24 mt-24">
                        <div class="flex-between gap-8">
                            <span class="text-gray-900 text-xl fw-semibold">Total</span>
                            <span class="text-gray-900 text-xl fw-semibold">₹479.2</span>
                        </div>
                    </div>  
                    @auth
                        <a href="/checkout" class="btn btn-main mt-40 py-18 w-100 rounded-8">
                            Proceed to checkout
                        </a>
                    @else
                        <a href="javascript:void(0)" onclick="showLoginAlert()"
                            class="btn btn-main mt-40 py-18 w-100 rounded-8">
                            Proceed to checkout
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
 </section>
<!-- ================================ Cart Section End ================================ -->

<script>

document.querySelectorAll('.quantity-box').forEach(box => {
    const input = box.querySelector('.quantity__input');
    const price = parseFloat(
        input.closest('tr').querySelector('td:nth-child(3) span').textContent.replace('₹', '')
    );
    const subtotalCell = input.closest('tr').querySelector('td:last-child span');

    function updateSubtotal() {
        const qty = parseInt(input.value) || 1;
        subtotalCell.textContent = '₹' + (price * qty).toFixed(2);
        updateCartTotal();
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

    input.addEventListener('input', updateSubtotal);
});

// ── Recalculate sidebar subtotal ─────────────────────────────────
function updateCartTotal() {
    let subtotal = 0;
    document.querySelectorAll('.quantity-box').forEach(box => {
        const input = box.querySelector('.quantity__input');
        const price = parseFloat(
            input.closest('tr').querySelector('td:nth-child(3) span').textContent.replace('₹', '')
        );
        subtotal += price * (parseInt(input.value) || 1);
    });

    // Update sidebar subtotal
    const subtotalEl = document.querySelector('.cart-sidebar .bg-color-three .fw-semibold');
    if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toFixed(2);

    // Update sidebar total (subtotal + 50 delivery + 16% tax)
    const delivery = 50;
    const tax = subtotal * 0.16;
    const total = subtotal + delivery + tax;
    const totalEls = document.querySelectorAll('.cart-sidebar .text-xl.fw-semibold');
    if (totalEls[1]) totalEls[1].textContent = '₹' + total.toFixed(2);
}

// ── Remove cart item ─────────────────────────────────────────────
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
</script>

<script>
function showLoginAlert() {
    Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please login to proceed to checkout',
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Login Now'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = "/login";
    });
}
</script>

@endsection