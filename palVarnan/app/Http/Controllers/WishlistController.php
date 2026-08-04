<?php
namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use App\Models\ProductVarient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $productId = $request->product_id;
        $variantId = $request->variant_id;

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('product_varient_id', $variantId)  
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'status' => 'removed']);
        }

        Wishlist::create([
            'user_id'            => Auth::id(),
            'ip_address'         => request()->ip(),
            'product_id'         => $productId,
            'product_varient_id' => $variantId,
            'product_quantity'   => 1,
        ]);

        return response()->json(['success' => true, 'status' => 'added']);
    }



    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
                ->with(['product.variants.unit', 'variant.unit'])
                ->get();

        return view('pages.wishlist', compact('items'));
    }

    public function remove($id)
    {
        Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
        return response()->json(['success' => true]);
    }

    public function checkProduct(Request $request)
    {
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_varient_id', '!=', $request->exclude_variant_id)  // ← correct column name
            ->with('variant.unit')
            ->first();

        if ($existing) {
            $v = $existing->variant;
            $existingLabel = $v->value . ($v->unit->short_name ?? '');

            $newVariant = ProductVarient::find($request->exclude_variant_id);
            $newLabel = $newVariant ? $newVariant->value . ($newVariant->unit->short_name ?? '') : '';

            return response()->json([
                'has_other_variant' => true,
                'existing_variant'  => $existingLabel,
                'new_variant'       => $newLabel,
            ]);
        }

        return response()->json(['has_other_variant' => false]);
    }

    public function check(Request $request)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_varient_id', $request->variant_id)  // ← correct column
            ->exists();

        return response()->json(['in_wishlist' => $exists]);
    }


}


