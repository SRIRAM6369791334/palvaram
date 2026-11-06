-- =====================================================
-- PalVarnan DB Schema Patch for TiDB Cloud
-- Generated: 2026-11-06 07:38:33
-- Fixes: 3 tables with missing columns
-- =====================================================

SET NAMES utf8mb4;

-- contacts: add phone column
ALTER TABLE `contacts` ADD COLUMN `phone` VARCHAR(20) NULL DEFAULT NULL;

-- product_order_items: add product_image column
ALTER TABLE `product_order_items` ADD COLUMN `product_image` VARCHAR(255) NULL DEFAULT NULL;

-- product_orders: add missing columns
ALTER TABLE `product_orders`
  ADD COLUMN `billing_state` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `shipping_state` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `coupon_discount` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  ADD COLUMN `shiprocket_order_id` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `shiprocket_shipment_id` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `awb_code` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `courier_name` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `pickup_scheduled` TINYINT(1) NULL DEFAULT '0',
  ADD COLUMN `manifest_url` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `label_url` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `invoice_url` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `shiprocket_status` VARCHAR(255) NULL DEFAULT NULL,
  ADD COLUMN `cancellation_reason` TEXT NULL DEFAULT NULL;

