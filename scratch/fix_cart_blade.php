<?php
$file = __DIR__ . '/../palVarnan/resources/views/pages/cart.blade.php';
$content = file_get_contents($file);

// 1. Add data attributes to row
$oldTr = "@forelse(\$cartItems as \$item)\r\n\r\n                                <tr>";
$newTr = "@forelse(\$cartItems as \$item)\r\n\r\n                                <tr data-variant-id=\"{{ \$item->product_varient_id }}\" data-cart-id=\"{{ \$item->id }}\">";

if (strpos($content, "@forelse(\$cartItems as \$item)") !== false) {
    // Try both CRLF and LF
    $content = preg_replace(
        '/@forelse\(\$cartItems as \$item\)\s*<tr>/',
        "@forelse(\$cartItems as \$item)\n\n                                <tr data-variant-id=\"{{ \$item->product_varient_id }}\" data-cart-id=\"{{ \$item->id }}\">",
        $content
    );
}

// 2. Replace Sidebar Totals & Dynamic Calculation
$sidebarOld = <<<'HTML'
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
HTML;

$sidebarNew = <<<'HTML'
                    @php
                        $delivery = $subtotal > 0 ? 50.00 : 0.00;
                        $tax = round($subtotal * 0.16, 2);
                        $grandTotal = $subtotal + $delivery + $tax;
                    @endphp
                    <div class="bg-color-three rounded-8 p-24">
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Subtotal</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-subtotal">
                                ₹{{ number_format($subtotal, 2) }}
                            </span>
                        </div>
                        <div class="mb-32 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Estimated Delivery</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-delivery">
                                ₹{{ number_format($delivery, 2) }}
                            </span>
                        </div>
                        <div class="mb-0 flex-between gap-8">
                            <span class="text-gray-900 font-heading-two">Estimated Taxes (16%)</span>
                            <span class="text-gray-900 fw-semibold" id="cart-sidebar-tax">
                                ₹{{ number_format($tax, 2) }}
                            </span>
                        </div>
                    </div>  
                    <div class="bg-color-three rounded-8 p-24 mt-24">
                        <div class="flex-between gap-8">
                            <span class="text-gray-900 text-xl fw-semibold">Total</span>
                            <span class="text-gray-900 text-xl fw-bold text-main-600" id="cart-sidebar-total">
                                ₹{{ number_format($grandTotal, 2) }}
                            </span>
                        </div>
                    </div>
HTML;

$content = str_replace($sidebarOld, $sidebarNew, $content);
// If not exact match due to line breaks, do normalized replace
if (strpos($content, 'cart-sidebar-total') === false) {
    $pattern = '/<div class="bg-color-three rounded-8 p-24">.*?₹479\.2.*?<\/div>\s*<\/div>/s';
    $content = preg_replace($pattern, $sidebarNew, $content);
}

// 3. Update JavaScript for live quantity sync, debounced AJAX, and dynamic totals
$jsReplacement = <<<'HTML'
<script>
let syncTimeout = null;
function debouncedSyncQuantity(variantId, qty) {
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
            if (data.success) {
                // Also trigger subtotal check for header sync
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
        const qty = parseInt(input.value) || 1;
        subtotalCell.textContent = '₹' + (price * qty).toFixed(2);
        updateCartTotal();
        const variantId = row.dataset.variantId;
        if (variantId) {
            debouncedSyncQuantity(variantId, qty);
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
    const grandTotal = roundToTwo(subtotal + delivery + tax);

    const subtotalEl = document.getElementById('cart-sidebar-subtotal');
    if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toFixed(2);

    const deliveryEl = document.getElementById('cart-sidebar-delivery');
    if (deliveryEl) deliveryEl.textContent = '₹' + delivery.toFixed(2);

    const taxEl = document.getElementById('cart-sidebar-tax');
    if (taxEl) taxEl.textContent = '₹' + tax.toFixed(2);

    const totalEl = document.getElementById('cart-sidebar-total');
    if (totalEl) totalEl.textContent = '₹' + grandTotal.toFixed(2);

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
</script>
HTML;

// Replace from <script> to end
$pos = strpos($content, '<script>');
if ($pos !== false) {
    $content = substr($content, 0, $pos) . $jsReplacement . "\n\n@endsection";
}

file_put_contents($file, $content);
echo "cart.blade.php updated successfully!\n";
