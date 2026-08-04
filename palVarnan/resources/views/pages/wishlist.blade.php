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

    <!-- ================================ Cart Section Start ================================ -->
<section class="cart py-80">
    <div class="container container-lg">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="cart-table border border-gray-100 rounded-8">
                    <div class="overflow-x-auto scroll-sm scroll-sm-horizontal">
                        <table class="table rounded-8 overflow-hidden">
                            <thead>
                                <tr class="border-bottom border-neutral-100">
                                    <th class="h6 mb-0 text-lg fw-bold px-16 py-12 border-end border-neutral-100">Delete</th>
                                    <th class="h6 mb-0 text-lg fw-bold px-16 py-12 border-end border-neutral-100">Product Name</th>
                                    <th class="h6 mb-0 text-lg fw-bold px-16 py-12 border-end border-neutral-100">Unit Price</th>
                                    <th class="h6 mb-0 text-lg fw-bold px-16 py-12 border-end border-neutral-100">Value</th>
                                    <th class="h6 mb-0 text-lg fw-bold px-16 py-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php
                                        $variant = $item->variant; 
                                    @endphp
                                <tr>
                                    <td class="px-16 py-12 border-end border-neutral-100">
                                        <button type="button"
                                            class="remove-wishlist-btn flex-align gap-12 hover-text-danger-600"
                                            data-id="{{ $item->id }}">
                                            <i class="ph ph-x-circle text-2xl d-flex"></i>
                                        </button>
                                    </td>
                                    <td class="px-16 py-12 border-end border-neutral-100">
                                        <div class="table-product d-flex align-items-center gap-12">
                                            <a href="/product/{{ $item->product->slug }}"
                                                class="table-product__thumb border border-gray-100 rounded-8 flex-center">
                                                <img src="{{ env('MAIN_URL') }}images/{{ $item->product->product_image }}" alt="">
                                            </a>
                                            <div class="table-product__content text-start">
                                                <h6 class="title text-lg fw-semibold mb-8">
                                                    <a href="/product/{{ $item->product->slug }}" class="link text-line-2">
                                                        {{ $item->product->product_name }}
                                                    </a>
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-16 py-12 border-end border-neutral-100">
                                        <span class="text-lg h6 mb-0 fw-semibold">
                                            ₹{{ $variant->offer_price ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-16 py-12 border-end border-neutral-100">
                                        @if($variant && !empty($variant->value) && $variant->unit)
                                            <span class="text-sm fw-semibold text-gray-700 border border-gray-100 rounded-8 px-10 py-4 d-inline-block">
                                                {{ $variant->value }}{{ $variant->unit->short_name ?? '' }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-16 py-12">
                                        @auth
                                        <button type="button"
                                            class="add-to-cart-from-wishlist btn btn-main-two rounded-8 px-24 py-8"
                                            data-product-id="{{ $item->product->id }}"
                                            data-variant-id="{{ $item->product_varient_id }}">
                                            Add To Cart <i class="ph ph-shopping-cart"></i>
                                        </button>
                                        @endauth
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-40 text-gray-500">Your wishlist is empty.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </section>
<!-- ================================ Cart Section End ================================ -->

<script>
document.querySelectorAll('.remove-wishlist-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const id  = this.dataset.id;
        const row = this.closest('tr');

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
                row.remove();

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
});

document.querySelectorAll('.add-to-cart-from-wishlist').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const variantId = this.dataset.variantId;
        const self      = this;

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
                title: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            // Optionally change button to "Go to Cart"
            self.innerHTML = '<i class="ph ph-check"></i> Added to Cart';
            self.disabled = true;
            self.classList.add('btn-success');
            self.classList.remove('btn-main-two');
        });
    });
});
</script>

@endsection