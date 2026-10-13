<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiprocketAndRazorpayColumnsToProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('product_orders', 'razorpay_order_id')) {
                $table->string('razorpay_order_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('product_orders', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            }
            if (!Schema::hasColumn('product_orders', 'shiprocket_order_id')) {
                $table->string('shiprocket_order_id')->nullable()->after('razorpay_payment_id');
            }
            if (!Schema::hasColumn('product_orders', 'shiprocket_shipment_id')) {
                $table->string('shiprocket_shipment_id')->nullable()->after('shiprocket_order_id');
            }
            if (!Schema::hasColumn('product_orders', 'awb_code')) {
                $table->string('awb_code')->nullable()->after('shiprocket_shipment_id');
            }
            if (!Schema::hasColumn('product_orders', 'courier_name')) {
                $table->string('courier_name')->nullable()->after('awb_code');
            }
            if (!Schema::hasColumn('product_orders', 'pickup_scheduled')) {
                $table->boolean('pickup_scheduled')->default(0)->after('courier_name');
            }
            if (!Schema::hasColumn('product_orders', 'manifest_url')) {
                $table->string('manifest_url')->nullable()->after('pickup_scheduled');
            }
            if (!Schema::hasColumn('product_orders', 'label_url')) {
                $table->string('label_url')->nullable()->after('manifest_url');
            }
            if (!Schema::hasColumn('product_orders', 'invoice_url')) {
                $table->string('invoice_url')->nullable()->after('label_url');
            }
            if (!Schema::hasColumn('product_orders', 'shiprocket_status')) {
                $table->string('shiprocket_status')->nullable()->after('invoice_url');
            }
            if (!Schema::hasColumn('product_orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('shiprocket_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $columns = [
                'razorpay_order_id',
                'razorpay_payment_id',
                'shiprocket_order_id',
                'shiprocket_shipment_id',
                'awb_code',
                'courier_name',
                'pickup_scheduled',
                'manifest_url',
                'label_url',
                'invoice_url',
                'shiprocket_status',
                'cancellation_reason'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('product_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
