path = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\pages\checkout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()

# Replace hardcoded ₹ in Blade template
c = c.replace("₹{{ number_format(($item->variant->offer_price", "{{ $currencySymbol ?? '₹' }}{{ number_format(($item->variant->offer_price")
c = c.replace("â‚¹{{ number_format(($item->variant->offer_price", "{{ $currencySymbol ?? '₹' }}{{ number_format(($item->variant->offer_price")

c = c.replace("₹{{ number_format($subtotal, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($subtotal, 2) }}")
c = c.replace("â‚¹{{ number_format($subtotal, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($subtotal, 2) }}")

c = c.replace("₹{{ number_format($totalGst, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($totalGst, 2) }}")
c = c.replace("â‚¹{{ number_format($totalGst, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($totalGst, 2) }}")

c = c.replace('id="display-shipping-charge">₹0.00</span>', 'id="display-shipping-charge">{{ $currencySymbol ?? "₹" }}0.00</span>')
c = c.replace('id="display-shipping-charge">â‚¹0.00</span>', 'id="display-shipping-charge">{{ $currencySymbol ?? "₹" }}0.00</span>')

c = c.replace("₹{{ number_format($subtotal + $totalGst, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($subtotal + $totalGst, 2) }}")
c = c.replace("â‚¹{{ number_format($subtotal + $totalGst, 2) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($subtotal + $totalGst, 2) }}")

# Replace JS formatting
c = c.replace("document.getElementById('display-shipping-charge').innerText = '₹' + currentShippingCharge.toFixed(2);", "const currSymbol = '{{ $currencySymbol ?? \"₹\" }}'; document.getElementById('display-shipping-charge').innerText = currSymbol + currentShippingCharge.toFixed(2);")
c = c.replace("document.getElementById('display-shipping-charge').innerText = 'â‚¹' + currentShippingCharge.toFixed(2);", "const currSymbol = '{{ $currencySymbol ?? \"₹\" }}'; document.getElementById('display-shipping-charge').innerText = currSymbol + currentShippingCharge.toFixed(2);")

c = c.replace("document.getElementById('display-grand-total').innerText = '₹' + grandTotal.toFixed(2);", "document.getElementById('display-grand-total').innerText = (typeof currSymbol !== 'undefined' ? currSymbol : '{{ $currencySymbol ?? \"₹\" }}') + grandTotal.toFixed(2);")
c = c.replace("document.getElementById('display-grand-total').innerText = 'â‚¹' + grandTotal.toFixed(2);", "document.getElementById('display-grand-total').innerText = (typeof currSymbol !== 'undefined' ? currSymbol : '{{ $currencySymbol ?? \"₹\" }}') + grandTotal.toFixed(2);")

c = c.replace('₹${parseFloat(courier.rate).toFixed(2)}', '${currSymbol}${parseFloat(courier.rate).toFixed(2)}')
c = c.replace('â‚¹${parseFloat(courier.rate).toFixed(2)}', '${currSymbol}${parseFloat(courier.rate).toFixed(2)}')

with open(path, 'w', encoding='utf-8') as f:
    f.write(c)

print("SUCCESS_PATCHED_CHECKOUT_CURRENCY")
