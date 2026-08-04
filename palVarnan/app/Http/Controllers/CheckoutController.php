<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // Selected address: from query string, else most recent, else null
        $selectedAddressId = $request->query('address_id', $addresses->first()->id ?? null);

        $cartItems = $this->getCartItems();

        $subtotal = $cartItems->sum(
            fn ($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity
        );

        return view('pages.checkout', compact('user', 'addresses', 'selectedAddressId', 'cartItems', 'subtotal'));
    }

public function storeAddress(Request $request)
{
    $validated = $this->validateAddress($request);

    try {
        $address = UserAddress::create([
            'user_id'              => Auth::id(),
            'address_first_name'   => $validated['first_name'],
            'address_last_name'    => $validated['last_name'],
            'address_phone_number' => $validated['phone_number'],
            'email'                => $validated['email'],
            'address_line_one'     => $validated['address_line_one'],
            'address_line_two'     => $validated['address_line_two'] ?? null,
            'landmark'             => $validated['landmark'] ?? null,
            'city'                 => $validated['city'],
            'state'                => $validated['state'],
            'pincode'              => $validated['pincode'],
        ]);
    } catch (\Throwable $e) {
        \Log::error('Address save failed: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Could not save address.'], 422);
        }
        return back()->withInput()->with('error', 'Could not save address.');
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Address added.',
            'address' => $address,
        ]);
    }

    return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address added.');
}

public function updateAddress(Request $request, UserAddress $address)
{
    abort_unless($address->user_id === Auth::id(), 403);

    $validated = $this->validateAddress($request);

    $address->update([
        'address_first_name'   => $validated['first_name'],
        'address_last_name'    => $validated['last_name'],
        'address_phone_number' => $validated['phone_number'],
        'email'                => $validated['email'],
        'address_line_one'     => $validated['address_line_one'],
        'address_line_two'     => $validated['address_line_two'] ?? null,
        'landmark'             => $validated['landmark'] ?? null,
        'city'                 => $validated['city'],
        'state'                => $validated['state'],
        'pincode'              => $validated['pincode'],
    ]);

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Address updated.',
            'address' => $address,
        ]);
    }

    return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address updated.');
}

    public function destroyAddress(UserAddress $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        $address->delete();

        return back()->with('success', 'Address removed.');
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => ['required', 'exists:user_addresses,id'],
        ]);

        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        // TODO: create Order + OrderItems here using $address and $cartItems,
        // then clear the cart. Send me your orders schema and I'll wire this up.

        return redirect()->route('checkout.index')->with('success', 'Order placed successfully!');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'first_name'               => ['required', 'string', 'max:255'],
            'last_name'                => ['required', 'string', 'max:255'],
            'phone_number'                    => ['required', 'string', 'max:20'],
            'email'                    => ['required', 'email', 'max:255'],
            'address_line_one'         => ['required', 'string', 'max:255'],
            'address_line_two'         => ['nullable', 'string', 'max:255'],
            'landmark'                 => ['nullable', 'string', 'max:255'],
            'city'                     => ['required', 'string', 'max:255'],
            'state'                    => ['required', 'string', 'max:255'],
            'pincode'                  => ['required', 'string', 'max:10'],
            // 'address_type_id'          => ['required', 'in:1,2,3'],
            // 'address_type_others_name' => ['nullable', 'required_if:address_type_id,3', 'string', 'max:255'],
        ]);
    }

    private function addressTypeName(int $typeId): string
    {
        return match ($typeId) {
            1 => 'Home',
            2 => 'Work',
            default => 'Others',
        };
    }

    private function getCartItems()
    {
        $query = Cart::with(['product', 'variant']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
                  ->whereNull('user_id');
        }

        return $query->whereHas('variant')->get();
    }
}