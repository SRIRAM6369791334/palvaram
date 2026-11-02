<?php
$file = __DIR__ . '/../palVarnan/app/Http/Controllers/WishlistController.php';

$code = <<<'PHP'
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
        if (!Auth::check()) {
            return response()->json([
                'success'       => false,
                'require_login' => true,
                'message'       => 'Please login to save items to your wishlist.'
            ], 401);
        }

        $productId = $request->product_id;
        $variantId = $request->variant_id;

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('product_varient_id', $variantId)
            ->first();

        if ($existing) {
            $existing->delete();
            $count = Wishlist::where('user_id', Auth::id())->count();
            return response()->json(['success' => true, 'status' => 'removed', 'count' => $count]);
        }

        Wishlist::create([
            'user_id'            => Auth::id(),
            'ip_address'         => request()->ip(),
            'product_id'         => $productId,
            'product_varient_id' => $variantId,
            'product_quantity'   => 1,
        ]);

        $count = Wishlist::where('user_id', Auth::id())->count();
        return response()->json(['success' => true, 'status' => 'added', 'count' => $count]);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login?redirect=/wishlist')->with('info', 'Please login to view your wishlist.');
        }

        $items = Wishlist::where('user_id', Auth::id())
            ->whereHas('product')
            ->with([
                'product.variants.unit',
                'product.variants.size',
                'product.variants.color',
                'variant.unit',
                'variant.size',
                'variant.color'
            ])
            ->get();

        return view('pages.wishlist', compact('items'));
    }

    public function remove($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
        $count = Wishlist::where('user_id', Auth::id())->count();
        return response()->json(['success' => true, 'count' => $count]);
    }

    public function checkProduct(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['has_other_variant' => false]);
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_varient_id', '!=', $request->exclude_variant_id)
            ->with(['variant.unit', 'variant.size', 'variant.color'])
            ->first();

        if ($existing) {
            $v = $existing->variant;
            if ($v->size || $v->color) {
                $parts = [];
                if ($v->size) $parts[] = 'Size: ' . $v->size->size;
                if ($v->color) $parts[] = 'Color: ' . $v->color->color;
                $existingLabel = !empty($parts) ? implode(' | ', $parts) : ($v->varient_name ?: 'Gift Item');
            } else {
                $existingLabel = ($v->value ?? '') . ($v->unit->short_name ?? '');
            }

            $newVariant = ProductVarient::with(['unit', 'size', 'color'])->find($request->exclude_variant_id);
            if ($newVariant) {
                if ($newVariant->size || $newVariant->color) {
                    $parts = [];
                    if ($newVariant->size) $parts[] = 'Size: ' . $newVariant->size->size;
                    if ($newVariant->color) $parts[] = 'Color: ' . $newVariant->color->color;
                    $newLabel = !empty($parts) ? implode(' | ', $parts) : ($newVariant->varient_name ?: 'Gift Item');
                } else {
                    $newLabel = ($newVariant->value ?? '') . ($newVariant->unit->short_name ?? '');
                }
            } else {
                $newLabel = '';
            }

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
        if (!Auth::check()) {
            return response()->json(['in_wishlist' => false]);
        }

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_varient_id', $request->variant_id)
            ->exists();

        return response()->json(['in_wishlist' => $exists]);
    }
}
PHP;

file_put_contents($file, $code);
echo "WishlistController.php upgraded successfully!\n";
