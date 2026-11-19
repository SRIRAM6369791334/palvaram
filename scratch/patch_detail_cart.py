with open('palVarnan/resources/views/pages/product-detail.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

old_sync = """// Sync quantity via AJAX
function syncCart(variantId, quantity, input) {
    fetch("{{ route('cart.ajaxUpdate') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ product_varient_id: variantId, quantity: quantity })
    }).then(r => r.json()).then(data => {
        if (data.success) input.value = quantity;
    });
}"""

new_sync = """// Sync quantity via AJAX
function syncCart(variantId, quantity, input) {
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
}"""

old_attach = """function attachAddToCartEvent(controlDiv) {
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
}"""

new_attach = """function attachAddToCartEvent(controlDiv) {
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
        .then(async r => {
            const data = await r.json();
            if (r.ok && data.status !== 'error') {
                showQtyBox(controlDiv, 1);
                updateCartCount(1);
                Swal.fire({ icon: 'success', title: data.message || 'Added to cart successfully!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
            } else {
                Swal.fire({ icon: 'warning', title: data.message || 'This product variant is currently unavailable.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            }
        }).catch(() => {
            Swal.fire({ icon: 'error', title: 'Network error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        });
    });
}"""

old_sync_crlf = old_sync.replace('\n', '\r\n')
new_sync_crlf = new_sync.replace('\n', '\r\n')
if old_sync in text:
    text = text.replace(old_sync, new_sync, 1)
elif old_sync_crlf in text:
    text = text.replace(old_sync_crlf, new_sync_crlf, 1)

old_attach_crlf = old_attach.replace('\n', '\r\n')
new_attach_crlf = new_attach.replace('\n', '\r\n')
if old_attach in text:
    text = text.replace(old_attach, new_attach, 1)
elif old_attach_crlf in text:
    text = text.replace(old_attach_crlf, new_attach_crlf, 1)

with open('palVarnan/resources/views/pages/product-detail.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("product-detail.blade.php successfully updated!")
