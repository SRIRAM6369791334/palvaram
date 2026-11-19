import re

# 1. Patch products.blade.php
with open('palVarnan/resources/views/pages/products.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Add data-stock to variant-option
old_opt = """data-offer="{{ $v->offer_price }}"
                                                                    data-mrp="{{ $v->mrp_price }}">
                                                                    <span class="opt-size">{{ $vLabel }}</span>"""
new_opt = """data-offer="{{ $v->offer_price }}"
                                                                    data-mrp="{{ $v->mrp_price }}"
                                                                    data-stock="{{ (int)($v->product_qty ?? 0) }}">
                                                                    <span class="opt-size">{{ $vLabel }}@if(($v->product_qty ?? 0) <= 0) <small class="text-danger-600 fw-bold ms-1" style="font-size:10px;">(Out)</small>@endif</span>"""

old_opt_crlf = old_opt.replace('\n', '\r\n')
new_opt_crlf = new_opt.replace('\n', '\r\n')

if old_opt in text:
    text = text.replace(old_opt, new_opt, 1)
elif old_opt_crlf in text:
    text = text.replace(old_opt_crlf, new_opt_crlf, 1)
else:
    print("Warning: old_opt not found in products.blade.php")

# Initial card button stock check
old_ctrl = """                                        @php
                                            $inCart   = $variant ? in_array($variant->id, $cartProductIds) : false;
                                            $cartItem = $variant ? $cartItems->where('product_varient_id', $variant->id)->first() : null;
                                        @endphp

                                        <div class="cart-control mt-8" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id ?? '' }}">
                                            @if($inCart)"""

new_ctrl = """                                        @php
                                            $inCart   = $variant ? in_array($variant->id, $cartProductIds) : false;
                                            $cartItem = $variant ? $cartItems->where('product_varient_id', $variant->id)->first() : null;
                                            $isOutOfStock = ($variant ? (int)($variant->product_qty ?? 0) : 0) <= 0;
                                        @endphp

                                        <div class="cart-control mt-8" data-product-id="{{ $product->id }}" data-variant-id="{{ $variant->id ?? '' }}">
                                            @if($isOutOfStock)
                                                <button type="button" class="btn btn-outline-neutral-300 py-10 px-16 rounded-pill w-100 justify-content-center text-sm fw-semibold text-neutral-400" disabled>
                                                    Out of Stock
                                                </button>
                                            @elseif($inCart)"""

old_ctrl_crlf = old_ctrl.replace('\n', '\r\n')
new_ctrl_crlf = new_ctrl.replace('\n', '\r\n')

if old_ctrl in text:
    text = text.replace(old_ctrl, new_ctrl, 1)
elif old_ctrl_crlf in text:
    text = text.replace(old_ctrl_crlf, new_ctrl_crlf, 1)
else:
    print("Warning: old_ctrl not found in products.blade.php")

# JS: update syncCart & attachAddToCartEvent
old_js = """function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity;
    });
}

function attachAddToCartEvent(controlDiv) {
    const btn = controlDiv.querySelector('.add-to-cart-btn');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
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
                icon: 'success', title: data.message || 'Added to cart successfully!',
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2000, timerProgressBar: true
            });
        });
    });
}"""

new_js = """function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(async r => {
        const data = await r.json();
        if (r.ok && data.success) {
            input.value = data.quantity !== undefined ? data.quantity : quantity;
            if (data.clamped) {
                Swal.fire({ icon: 'info', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
            }
        } else {
            Swal.fire({ icon: 'warning', title: data.message || 'Could not update quantity', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
        }
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Network error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    });
}

function attachAddToCartEvent(controlDiv) {
    const btn = controlDiv.querySelector('.add-to-cart-btn');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = controlDiv.dataset.productId;
        const variantId = controlDiv.dataset.variantId;

        fetch("{{ route('cart.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId, product_varient_id: variantId, quantity: 1 })
        }).then(async r => {
            const data = await r.json();
            if (r.ok && data.status !== 'error') {
                showQtyBox(controlDiv, 1);
                Swal.fire({
                    icon: 'success', title: data.message || 'Added to cart successfully!',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true
                });
            } else {
                Swal.fire({
                    icon: 'warning', title: data.message || 'This product variant is currently unavailable.',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 3000, timerProgressBar: true
                });
            }
        }).catch(() => {
            Swal.fire({
                icon: 'error', title: 'Network error',
                toast: true, position: 'top-end',
                showConfirmButton: false, timer: 2000
            });
        });
    });
}"""

old_js_crlf = old_js.replace('\n', '\r\n')
new_js_crlf = new_js.replace('\n', '\r\n')

if old_js in text:
    text = text.replace(old_js, new_js, 1)
elif old_js_crlf in text:
    text = text.replace(old_js_crlf, new_js_crlf, 1)
else:
    print("Warning: old_js not found in products.blade.php")

# JS: update applyVariant to also respect stock
old_apply = """        const control = card.querySelector('.cart-control');
        if (control) {
            control.dataset.variantId = variantId;
            if (control.querySelector('.qty-box')) {
                control.innerHTML = `
                    <button type="button"
                        class="add-to-cart-btn product-card__cart btn py-10 px-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-sm fw-semibold">
                        <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-base"></i>
                    </button>`;
                attachAddToCartEvent(control);
            }
        }"""

new_apply = """        const control = card.querySelector('.cart-control');
        if (control) {
            control.dataset.variantId = variantId;
            const stock = parseInt(option.dataset.stock !== undefined ? option.dataset.stock : 99);
            if (stock <= 0) {
                control.innerHTML = `
                    <button type="button" class="btn btn-outline-neutral-300 py-10 px-16 rounded-pill w-100 justify-content-center text-sm fw-semibold text-neutral-400" disabled>
                        Out of Stock
                    </button>`;
            } else {
                control.innerHTML = `
                    <button type="button"
                        class="add-to-cart-btn product-card__cart btn py-10 px-16 rounded-pill flex-align gap-6 w-100 justify-content-center text-sm fw-semibold">
                        <span>Add To Cart</span> <i class="ph-bold ph-shopping-cart-simple text-base"></i>
                    </button>`;
                attachAddToCartEvent(control);
            }
        }"""

old_apply_crlf = old_apply.replace('\n', '\r\n')
new_apply_crlf = new_apply.replace('\n', '\r\n')

if old_apply in text:
    text = text.replace(old_apply, new_apply, 1)
elif old_apply_crlf in text:
    text = text.replace(old_apply_crlf, new_apply_crlf, 1)
else:
    print("Warning: old_apply not found in products.blade.php")

with open('palVarnan/resources/views/pages/products.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("products.blade.php successfully updated!")
