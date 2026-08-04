<?php

namespace App\Http\Controllers;

use App\Models\Cart;
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

        $cartItems = $query->whereHas('variant')->get();

        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item->variant->offer_price * $item->product_quantity;
        }

        return view('pages.cart', compact('cartItems', 'subtotal'));
    }

    public function update(Request $request)
    {
        foreach ($request->quantities as $cartId => $qty) {

            Cart::where('id', $cartId)
                ->update([
                    'product_quantity' => $qty
                ]);
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
            $existing->increment('product_quantity', 1);
            return response()->json(['status' => 'incremented', 'message' => 'Cart updated!']);
        } else {
            Cart::create([
                'user_id'            => Auth::id(), // null for guests
                'ip_addresscart'     => $request->ip(),
                'product_id'         => $request->product_id,
                'product_varient_id' => $request->product_varient_id,
                'product_quantity'   => $request->quantity ?? 1,
                'pre_order'          => 0,
            ]);
            return response()->json(['status' => 'added', 'message' => 'Product added to cart!', 'quantity' => 1]);
        }
    }

    public function ajaxUpdate(Request $request)
    {
        $query = Cart::where('product_varient_id', $request->product_varient_id);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', $request->ip())->whereNull('user_id');
        }

        $cart = $query->first();

        if ($cart) {
            $cart->update(['product_quantity' => $request->quantity]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
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
    $query = Cart::with('variant');

    if (Auth::check()) {
        $query->where('user_id', Auth::id());
    } else {
        $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
              ->whereNull('user_id');
    }

    $subtotal = $query->whereHas('variant')->get()
        ->sum(fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity);

    return response()->json(['subtotal' => $subtotal]);
}

}