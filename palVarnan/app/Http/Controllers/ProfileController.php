<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductOrder;
use App\Models\ProductSlot;
use App\Models\ProductStock;
use App\Models\ProductTracking;
use App\Models\ProductVarient;
use App\Models\Wishlist;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable',
        ]);

        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->filled('password')) {

            $request->validate([
                'current_password' => 'required',
                'password' => 'required|confirmed|min:6',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is wrong')->with('tab', 'personal');
            }

            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        return back()->with('success', 'Profile updated successfully')->with('tab', 'personal');
    }

    public function logout(Request $request)
    {

        $countryId = session('country_id');

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        session(['country_id' => $countryId]);

        return redirect('/')->with('success', 'Logged out successfully');
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Please sign in to access your account.');
        }

        $userId = Auth::id();

        $wishlistItems = Wishlist::where('user_id', $userId)
            ->whereHas('product')
            ->with(['product.variants', 'variant.unit'])
            ->get();

        $cartItems = Cart::where('user_id', $userId)
            ->whereHas('product')
            ->with(['product', 'variant.unit'])
            ->get();

        $orders = ProductOrder::where('user_id', $userId)
            ->with(['items.product', 'items.variant'])
            ->latest()
            ->get();

        $addresses = \App\Models\UserAddress::where('user_id', $userId)
            ->latest()
            ->get();

        $states = \App\Models\State::all();

        $cartSubtotal = $cartItems->sum(fn($item) => ($item->variant->offer_price ?? $item->product->product_price ?? 0) * $item->product_quantity);

        return view('pages.myaccount', compact('wishlistItems', 'cartItems', 'cartSubtotal', 'orders', 'addresses', 'states'));
    }

    public function cancelOrder(Request $request, $orderNumber)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $order = ProductOrder::with('items')->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $cancellableStatuses = ['order placed', 'pending'];
        if (!in_array(strtolower($order->status), $cancellableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => "Order cannot be cancelled because it is already {$order->status}."
            ], 400);
        }

        $reason = $request->input('reason', 'Cancelled by customer');

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'Cancelled',
                'cancellation_reason' => $reason
            ]);

            ProductSlot::where('order_id', $order->order_number)
                ->orWhere('order_id', $order->order_id)
                ->update([
                    'delivery_status' => 5,
                    'is_cancelled' => 1,
                    'cancel_reason' => $reason
                ]);

            ProductTracking::create([
                'user_id' => Auth::id(),
                'order_id' => $order->order_number,
                'delivery_status' => 5,
                'status' => 'Cancelled'
            ]);

            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $stock = ProductStock::where('pro_ver_id', $item->product_variant_id)->first();
                    if ($stock) {
                        $stock->availablestock += $item->quantity;
                        $stock->salestock = max(0, ($stock->salestock ?? 0) - $item->quantity);
                        $stock->save();
                    }

                    $variant = ProductVarient::find($item->product_variant_id);
                    if ($variant) {
                        $variant->product_qty += $item->quantity;
                        $variant->save();
                    }
                }
            }

            DB::commit();

            if ($order->shiprocket_order_id) {
                try {
                    $shiprocket = new ShiprocketService();
                    $shiprocket->cancelOrder(['ids' => [$order->shiprocket_order_id]]);
                } catch (\Throwable $srEx) {
                    Log::warning('Shiprocket cancel API error: ' . $srEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order cancellation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not cancel order: ' . $e->getMessage()], 500);
        }
    }

    public function returnOrder(Request $request, $orderNumber)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $order = ProductOrder::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (strtolower($order->status) !== 'delivered') {
            return response()->json(['success' => false, 'message' => 'Only delivered orders can be requested for return.'], 400);
        }

        $reason = $request->input('reason', 'Return requested by customer');

        $order->update([
            'status' => 'Return Requested',
            'reason' => $reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return request submitted successfully. Our team will contact you shortly.'
        ]);
    }
}



