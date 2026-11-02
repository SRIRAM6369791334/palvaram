path = r'c:\xampp\htdocs\pauls\palVarnan\app\Http\Controllers\CheckoutController.php'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Replace discount and shipping calculation
for i in range(275, 300):
    if '$shipping = floatval($request->shipping_charge ?? 0);' in lines[i]:
        lines[i] = '''        $shipping = max(0, floatval($request->shipping_charge ?? 0));
        // Security check: validate discount against authenticated session coupon to prevent client-side tampering (TC-CH01 / TC-CH11)
        $sessionDiscount = floatval(session('applied_coupon.discount', 0));
        $discount = min($sessionDiscount, max(0, floatval($request->coupon_discount ?? 0)));
        $total = max(0, ($subtotal + $gstAmount + $shipping) - $discount);
'''
        lines[i+1] = ''
        lines[i+2] = ''
        break

# Clear session coupon upon cart deletion
for i in range(410, 430):
    if 'Cart::where(\'user_id\', $userId)->delete();' in lines[i]:
        lines[i] += "            session()->forget('applied_coupon');\n"
        break

with open(path, 'w', encoding='utf-8') as f:
    f.writelines(lines)

print("SUCCESSFULLY_HARDENED_CHECKOUT")
