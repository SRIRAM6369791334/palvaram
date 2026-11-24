<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The storefront used to write 'Order Placed', 'Cancelled', 'Return Requested'
 * and 'Paid'/'Pending', while the admin dashboard writes and filters on the
 * lowercase codes ('pending', 'cancelled', 'paid', ...). Rewrite old rows to
 * the dashboard codes (see ProductOrder::STATUS_* constants).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_orders')->whereRaw("BINARY status = 'Order Placed'")->update(['status' => 'pending']);
        DB::table('product_orders')->whereRaw("BINARY status = 'Cancelled'")->update(['status' => 'cancelled']);
        DB::table('product_orders')->whereRaw("BINARY status = 'Return Requested'")->update(['status' => 'return_requested']);
        DB::table('product_orders')->whereRaw('BINARY payment_status <> BINARY LOWER(payment_status)')
            ->update(['payment_status' => DB::raw('LOWER(payment_status)')]);

        DB::table('product_tracking')->whereRaw("BINARY status = 'Cancelled'")->update(['status' => 'cancelled']);

        // Return requests made before the fix never set the tracking flag the
        // dashboard Return Requests page reads; flag the latest row per order.
        $orderNumbers = DB::table('product_orders')->where('status', 'return_requested')->pluck('order_number');
        foreach ($orderNumbers as $orderNumber) {
            $latestId = DB::table('product_tracking')->where('order_id', $orderNumber)->max('id');
            if ($latestId) {
                DB::table('product_tracking')->where('id', $latestId)
                    ->where('return_requested', 0)
                    ->update(['return_requested' => 1, 'status' => 'return_requested']);
            }
        }
    }

    public function down(): void
    {
        // Data normalisation only; the old mixed-case values are not restored.
    }
};
