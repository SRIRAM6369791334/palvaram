# 1. Patch app.blade.php
path_app = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\layouts\app.blade.php'
with open(path_app, 'r', encoding='utf-8') as f:
    c_app = f.read()

# Replace HTML subtotal
target_app_html = """        <div id="fcb-subtotal" style="font-size:14px;font-weight:600;color:#111827;line-height:1.3;">
            â‚¹{{ number_format($cartSubtotal, 0) }}
        </div>"""
replacement_app_html = """        <div id="fcb-subtotal" style="font-size:14px;font-weight:600;color:#111827;line-height:1.3;">
            {{ $currencySymbol ?? '₹' }}{{ number_format($cartSubtotal, 0) }}
        </div>"""

if target_app_html in c_app:
    c_app = c_app.replace(target_app_html, replacement_app_html)
    print("PATCHED_APP_HTML")
else:
    # Try with raw currency symbol
    c_app = c_app.replace("â‚¹{{ number_format($cartSubtotal, 0) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($cartSubtotal, 0) }}")
    c_app = c_app.replace("₹{{ number_format($cartSubtotal, 0) }}", "{{ $currencySymbol ?? '₹' }}{{ number_format($cartSubtotal, 0) }}")
    print("REPLACED_APP_HTML_ALT")

# Replace JS subtotal
c_app = c_app.replace("if (el) el.textContent = 'â‚¹' + Number(data.subtotal).toLocaleString('en-IN');", "const curr = '{{ $currencySymbol ?? \"₹\" }}'; if (el) el.textContent = curr + Number(data.subtotal).toLocaleString();")
c_app = c_app.replace("if (el) el.textContent = '₹' + Number(data.subtotal).toLocaleString('en-IN');", "const curr = '{{ $currencySymbol ?? \"₹\" }}'; if (el) el.textContent = curr + Number(data.subtotal).toLocaleString();")
c_app = c_app.replace("if (el) el.textContent = 'â‚¹' + amount.toLocaleString('en-IN');", "const curr = '{{ $currencySymbol ?? \"₹\" }}'; if (el) el.textContent = curr + amount.toLocaleString();")
c_app = c_app.replace("if (el) el.textContent = '₹' + amount.toLocaleString('en-IN');", "const curr = '{{ $currencySymbol ?? \"₹\" }}'; if (el) el.textContent = curr + amount.toLocaleString();")

with open(path_app, 'w', encoding='utf-8') as f:
    f.write(c_app)
print("APP_BLADE_SAVED")

# 2. Patch cart.blade.php
path_cart = r'c:\xampp\htdocs\pauls\palVarnan\resources\views\pages\cart.blade.php'
with open(path_cart, 'r', encoding='utf-8') as f:
    c_cart = f.read()

c_cart = c_cart.replace("₹{{ $item->variant->offer_price }}", "{{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price }}")
c_cart = c_cart.replace("â‚¹{{ $item->variant->offer_price }}", "{{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price }}")

c_cart = c_cart.replace("₹{{ $item->variant->offer_price * $item->product_quantity }}", "{{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price * $item->product_quantity }}")
c_cart = c_cart.replace("â‚¹{{ $item->variant->offer_price * $item->product_quantity }}", "{{ $currencySymbol ?? '₹' }}{{ $item->variant->offer_price * $item->product_quantity }}")

c_cart = c_cart.replace("-₹${parseFloat(data.discount).toFixed(2)}", "-${curr}${parseFloat(data.discount).toFixed(2)}")
c_cart = c_cart.replace("-â‚¹${parseFloat(data.discount).toFixed(2)}", "-${curr}${parseFloat(data.discount).toFixed(2)}")
c_cart = c_cart.replace("'-₹' + parseFloat(data.discount).toFixed(2)", "'-' + curr + parseFloat(data.discount).toFixed(2)")
c_cart = c_cart.replace("'-â‚¹' + parseFloat(data.discount).toFixed(2)", "'-' + curr + parseFloat(data.discount).toFixed(2)")

with open(path_cart, 'w', encoding='utf-8') as f:
    f.write(c_cart)
print("CART_BLADE_SAVED")
