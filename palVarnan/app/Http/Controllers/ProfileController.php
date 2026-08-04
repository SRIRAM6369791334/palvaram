<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
                return back()->with('error', 'Current password is wrong');
            }

            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        return back()->with('success', 'Profile updated successfully');
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
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with(['product.variants', 'variant.unit'])
            ->get();

        $cartItems = Cart::where('user_id', Auth::id())
            ->with(['product', 'variant.unit'])
            ->get();

        $cartSubtotal = $cartItems->sum(fn($item) => $item->variant->offer_price * $item->product_quantity);

        return view('pages.myaccount', compact('wishlistItems', 'cartItems', 'cartSubtotal'));
    }
}
