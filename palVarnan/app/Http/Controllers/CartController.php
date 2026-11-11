<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ProductVarient;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $query = Cart::with(['product', 'variant']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
          ->whereNull('user_id');
        }

        $cartItems = $query->whereHas('variant')->whereHas('product')->get();

        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item->variant->offer_price * $item->product_quantity;
        }

        return view('pages.cart', compact('cartItems', 'subtotal'));
    }

    public function update(Request $request)
    {
        if (is_array($request->quantities)) {
            foreach ($request->quantities as $cartId => $qty) {
                $cart = Cart::find($cartId);
                if (!$cart) continue;

                $variant = ProductVarient::find($cart->product_varient_id);
                $availableStock = $variant ? (int)$variant->product_qty : 99;
                
                $sanitizedQty = max(1, min((int)$qty, max(1, $availableStock)));
                $cart->update(['product_quantity' => $sanitizedQty]);
            }
        }

        return back()->with('success', 'Cart updated successfully');
    }

    public function remove($id)
    {
        $cart = Cart::findOrFail($id);

        $cart->delete();

        if (request()->ajax()) {

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart'
            ]);

        }

        return back()->with('success', 'Product removed from cart');
    }

    public function store(Request $request)
    {
        $variant = ProductVarient::find($request->product_varient_id);
        if (!$variant) {
            return response()->json(['status' => 'error', 'message' => 'Product variant not found.'], 404);
        }

        $availableStock = (int) ($variant->product_qty ?? 0);
        if ($availableStock <= 0) {
            return response()->json(['status' => 'error', 'message' => 'This product is currently out of stock.'], 400);
        }

        if (Auth::check()) {
            $existing = Cart::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->where('product_varient_id', $request->product_varient_id)
                ->first();
        } else {
            $existing = Cart::where('ip_addresscart', $request->ip())
                ->whereNull('user_id')
                ->where('product_id', $request->product_id)
                ->where('product_varient_id', $request->product_varient_id)
                ->first();
        }

        if ($existing) {
            if ($existing->product_quantity + 1 > $availableStock) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cannot add more. Only {$availableStock} items in stock."
                ], 400);
            }
            $existing->increment('product_quantity', 1);
            return response()->json(['status' => 'incremented', 'message' => 'Cart updated!']);
        } else {
            $requestedQty = max(1, min((int)($request->quantity ?? 1), $availableStock));
            Cart::create([
                'user_id'            => Auth::id(), // null for guests
                'ip_addresscart'     => $request->ip(),
                'product_id'         => $request->product_id,
                'product_varient_id' => $request->product_varient_id,
                'product_quantity'   => $requestedQty,
                'pre_order'          => 0,
            ]);
            return response()->json(['status' => 'added', 'message' => 'Product added to cart!', 'quantity' => $requestedQty]);
        }
    }

    public function ajaxUpdate(Request $request)
    {
        $variantId = $request->product_varient_id;
        $requestedQty = (int) $request->quantity;

        $query = Cart::where('product_varient_id', $variantId);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', $request->ip())->whereNull('user_id');
        }

        $cart = $query->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Item not found in cart.'], 404);
        }

        // TC-C01 / TC-C02: If quantity is 0 or negative, remove item or clamp to 1
        if ($requestedQty <= 0) {
            $cart->delete();
            return response()->json([
                'success' => true,
                'removed' => true,
                'message' => 'Item removed from cart'
            ]);
        }

        // TC-C04 / TC-C05: Check stock upper bound
        $variant = ProductVarient::find($variantId);
        $availableStock = $variant ? (int)$variant->product_qty : 99;

        if ($requestedQty > $availableStock) {
            $cart->update(['product_quantity' => $availableStock]);
            return response()->json([
                'success' => true,
                'clamped' => true,
                'quantity' => $availableStock,
                'message' => "Only {$availableStock} items available in stock. Quantity adjusted."
            ]);
        }

        $cart->update(['product_quantity' => $requestedQty]);
        return response()->json(['success' => true, 'quantity' => $requestedQty]);
    }

    public function ajaxRemove(Request $request)
    {
        $query = Cart::where('product_varient_id', $request->product_varient_id);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', $request->ip())->whereNull('user_id');
        }

        $query->delete();

        return response()->json(['success' => true]);
    }

public function subtotal()
{
    $query = Cart::with(['variant', 'product']);

    if (Auth::check()) {
        $query->where('user_id', Auth::id());
    } else {
        $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
              ->whereNull('user_id');
    }

    $subtotal = $query->whereHas('variant')->whereHas('product')->get()
        ->sum(fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity);

    // Revalidate session coupon min_amt
    $applied = session('applied_coupon');
    if ($applied && !empty($applied['id'])) {
        $coupon = Coupon::find($applied['id']);
        if ($coupon && !empty($coupon->mini_amt) && $subtotal < $coupon->mini_amt) {
            session()->forget('applied_coupon');
        }
    }

    return response()->json(['subtotal' => $subtotal]);
}


    public function applyCoupon(Request $request)
    {
        $code = trim($request->coupon_code);
        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Please enter a coupon code.'], 400);
        }

        // Case-insensitive lookup prevents case mismatch errors
        $coupon = Coupon::whereRaw('LOWER(codename) = ?', [strtolower($code)])
            ->where(function ($q) {
                $q->where('coupon_status', 1)->orWhereNull('coupon_status');
            })
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive coupon code.'], 404);
        }

        // Expiry check
        if (!empty($coupon->end_date) && strtotime($coupon->end_date) < strtotime(date('Y-m-d'))) {
            return response()->json(['success' => false, 'message' => 'This coupon has expired.'], 400);
        }

        // Get subtotal
        $query = Cart::with(['variant', 'product']);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())->whereNull('user_id');
        }
        $subtotal = $query->whereHas('variant')->whereHas('product')->get()
            ->sum(fn($i) => ($i->variant->offer_price ?? 0) * $i->product_quantity);

        // TC-C13: Minimum cart amount check
        if (!empty($coupon->mini_amt) && $subtotal < $coupon->mini_amt) {
            return response()->json([
                'success' => false,
                'message' => "Minimum cart value of ₹{$coupon->mini_amt} required for this coupon."
            ], 400);
        }

        $discount = 0;
        if ($coupon->discounttype == 1) { // Flat
            $discount = min($subtotal, (float)$coupon->discount);
        } elseif ($coupon->discounttype == 2) { // Percentage
            $discount = min($subtotal, round($subtotal * ((float)$coupon->discount / 100), 2));
        } else {
            $discount = min($subtotal, (float)$coupon->discount);
        }

        session(['applied_coupon' => [
            'code' => $coupon->codename,
            'discount' => $discount,
            'id' => $coupon->id
        ]]);

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'code' => $coupon->codename,
            'message' => "Coupon '{$coupon->codename}' applied successfully! ₹" . number_format($discount, 2) . " discount."
        ]);
    }

}
