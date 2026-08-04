<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductSlot;
use App\Models\ProductVarient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelProductController extends Controller {
    public function index() {
        $CancelOrders = ProductSlot::query()->with( 'order.customer.user_addresses', 'product', 'productVarient' )->where( 'is_cancelled', 2 )->get();

        return view( 'pages.cancelproduct', compact( 'CancelOrders' ) );
    }

    public function cancelProductrequ( Request $request ) {
        $productSlotId =   $request->product_slot_id;
        // $reasonForCancel = $request->reason_for_cancel;

        $productSlot = ProductSlot::findOrFail( $productSlotId );

        $orderId = $productSlot->order_id;
        $prover_id = $productSlot->product_varient_id;
        $proid = $productSlot->product_id;
        $quet = $productSlot->quantity;

        ProductRefund::create( [
            'order_id' => $orderId, 'slot_id' => $productSlotId, 'cancelled_by' => 'Admin', 'refund_status' => 0
        ] );

        $productscount = ProductSlot::where( 'order_id', $orderId )->where( 'is_cancelled', 2 )->count();

        if ( $productscount == 1 ) {
            $productSlot->update( [
                'is_cancelled' => 1,
                'delivery_status' => 5, // Keep as 5 for internal slot status if needed, but Order status is string
            ] );

            ProductOrder::where( 'order_id', $orderId )->update( [
                'status' => 'cancelled',
            ] );
        } else {
            $productSlot->update( [
                'is_cancelled' => 1,
                'delivery_status' => 5,
            ] );
        }

        $product =  DB::table( 'productstocks' )->where( 'productid', $proid )->where( 'pro_ver_id', $prover_id )->first();

        DB::table( 'productstocks' )->where( 'productid', $proid )->where( 'pro_ver_id', $prover_id )->update( [
            'overallstock' => $product->overallstock+$quet,
            'availablestock' => $product->availablestock+$quet,
            'salestock' => $product->salestock-$quet,

        ] );

        $provaer = ProductVarient::where( 'product_id', $proid )->where( 'id', $prover_id )->first();
        ProductVarient::where( 'product_id', $proid )->where( 'id', $prover_id )->update( [
            'product_qty' => $provaer->product_qty+$quet,
        ] );

        $CancelOrders = ProductSlot::query()->with( 'order.customer.user_addresses', 'product', 'productVarient' )->where( 'is_cancelled', 2 )->get();

        return response()->json( [
            'message' => 'Product Cancel Successfully',
            'CancelOrders' => $CancelOrders
        ] );
    }
}