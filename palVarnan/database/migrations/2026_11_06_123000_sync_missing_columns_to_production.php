<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sync all missing columns that exist in local DB but not in TiDB production.
     * Safe: uses hasColumn() checks so it can run multiple times without error.
     */
    public function up(): void
    {
        // 1. product_order_items: add product_image
        if (Schema::hasTable('product_order_items')) {
            if (!Schema::hasColumn('product_order_items', 'product_image')) {
                Schema::table('product_order_items', function (Blueprint $table) {
                    $table->string('product_image', 255)->nullable()->after('product_name');
                });
            }
        }

        // 2. product_orders: add billing_state, shipping_state, coupon_discount,
        //    shiprocket columns, and cancellation_reason
        if (Schema::hasTable('product_orders')) {
            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'billing_state')) {
                    $table->string('billing_state', 255)->nullable()->after('billing_city');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'shipping_state')) {
                    $table->string('shipping_state', 255)->nullable()->after('shipping_city');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'coupon_discount')) {
                    $table->decimal('coupon_discount', 10, 2)->default(0)->after('shipping_charge');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'shiprocket_order_id')) {
                    $table->string('shiprocket_order_id', 255)->nullable()->after('razorpay_payment_id');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'shiprocket_shipment_id')) {
                    $table->string('shiprocket_shipment_id', 255)->nullable()->after('shiprocket_order_id');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'awb_code')) {
                    $table->string('awb_code', 255)->nullable()->after('shiprocket_shipment_id');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'courier_name')) {
                    $table->string('courier_name', 255)->nullable()->after('awb_code');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'pickup_scheduled')) {
                    $table->boolean('pickup_scheduled')->default(false)->after('courier_name');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'manifest_url')) {
                    $table->string('manifest_url', 255)->nullable()->after('pickup_scheduled');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'label_url')) {
                    $table->string('label_url', 255)->nullable()->after('manifest_url');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'invoice_url')) {
                    $table->string('invoice_url', 255)->nullable()->after('label_url');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'shiprocket_status')) {
                    $table->string('shiprocket_status', 255)->nullable()->after('invoice_url');
                }
            });

            Schema::table('product_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('product_orders', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('shiprocket_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_order_items') && Schema::hasColumn('product_order_items', 'product_image')) {
            Schema::table('product_order_items', function (Blueprint $table) {
                $table->dropColumn('product_image');
            });
        }

        $orderCols = [
            'billing_state', 'shipping_state', 'coupon_discount',
            'shiprocket_order_id', 'shiprocket_shipment_id', 'awb_code',
            'courier_name', 'pickup_scheduled', 'manifest_url',
            'label_url', 'invoice_url', 'shiprocket_status', 'cancellation_reason',
        ];

        if (Schema::hasTable('product_orders')) {
            foreach ($orderCols as $col) {
                if (Schema::hasColumn('product_orders', $col)) {
                    Schema::table('product_orders', function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                }
            }
        }
    }
};
