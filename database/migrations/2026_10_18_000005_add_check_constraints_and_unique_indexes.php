<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Data saat ini memakai FIXED dan AREA (sesuai dokumen aturan bisnis). Samakan huruf besar/kecilnya
        // dan pulihkan FIX -> FIXED seandainya ada baris yang dibuat controller lama.
        DB::statement('UPDATE product_materials SET usage_method = UPPER(usage_method)');
        DB::statement("UPDATE product_materials SET usage_method = 'FIXED' WHERE usage_method = 'FIX'");

        // ---------- Nilai yang diperbolehkan ----------
        // Empat status pesanan sesuai alur yang disepakati (pembatalan belum termasuk lingkup).
        DB::statement("
            ALTER TABLE orders ADD CONSTRAINT orders_status_check
            CHECK (status IN ('NEW', 'WAITING_PRODUCTION', 'READY_TO_PACK', 'COMPLETED'))
        ");

        DB::statement("
            ALTER TABLE product_groups ADD CONSTRAINT product_groups_production_method_check
            CHECK (production_method IN ('MTS', 'MTO'))
        ");

        DB::statement("
            ALTER TABLE product_materials ADD CONSTRAINT product_materials_usage_method_check
            CHECK (usage_method IN ('FIXED', 'AREA'))
        ");

        DB::statement("
            ALTER TABLE raw_materials ADD CONSTRAINT raw_materials_material_type_check
            CHECK (material_type IN ('SHEET', 'NON_SHEET'))
        ");

        // Tabel production_requests masih kosong saat migrasi ini dibuat, jadi daftar status
        // ditetapkan sekarang. WAITING dan IN_PROCESS dianggap "aktif" oleh index unik di add_integrity_indexes.
        DB::statement("
            ALTER TABLE production_requests ADD CONSTRAINT production_requests_status_check
            CHECK (status IN ('WAITING', 'IN_PROCESS', 'COMPLETED'))
        ");

        // Sumber permintaan produksi: ORDER (memenuhi pesanan pelanggan, MTO atau kekurangan stok MTS)
        // atau RESTOCK (isi ulang stok MTS). ORDER wajib terhubung ke item pesanan, RESTOCK tidak boleh.
        DB::statement("
            ALTER TABLE production_requests ADD CONSTRAINT production_requests_source_check
            CHECK (
                (source = 'ORDER'   AND order_item_id IS NOT NULL) OR
                (source = 'RESTOCK' AND order_item_id IS NULL)
            )
        ");

        // ---------- Jenis pergerakan stok ----------
        // Data lama hanya berisi ADJUSTMENT (stok awal).
        DB::statement("
            ALTER TABLE product_stock_movements ADD CONSTRAINT product_stock_movements_type_check
            CHECK (movement_type IN ('IN', 'OUT', 'ADJUSTMENT'))
        ");
        DB::statement("
            ALTER TABLE raw_material_movements ADD CONSTRAINT raw_material_movements_type_check
            CHECK (movement_type IN ('IN', 'OUT', 'ADJUSTMENT'))
        ");

        // ---------- Jejak sumber transaksi ----------
        // Stock IN produk berasal dari log produksi MTS, Stock OUT produk dari item pesanan.
        // Stock IN bahan berasal dari penerimaan PO, Stock OUT bahan dari pemakaian pada log produksi.
        // ADJUSTMENT boleh tanpa sumber (dijelaskan lewat note, created_by, qty_before/qty_after).
        DB::statement("
            ALTER TABLE product_stock_movements ADD CONSTRAINT product_stock_movements_source_check
            CHECK (
                (movement_type = 'IN'  AND production_log_id IS NOT NULL) OR
                (movement_type = 'OUT' AND order_item_id IS NOT NULL) OR
                (movement_type = 'ADJUSTMENT')
            )
        ");
        DB::statement("
            ALTER TABLE raw_material_movements ADD CONSTRAINT raw_material_movements_source_check
            CHECK (
                (movement_type = 'IN'  AND purchase_order_item_id IS NOT NULL) OR
                (movement_type = 'OUT' AND production_log_material_id IS NOT NULL) OR
                (movement_type = 'ADJUSTMENT')
            )
        ");

        // ---------- Kuantitas ----------
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_qty_positive CHECK (qty > 0)');
        DB::statement('ALTER TABLE production_requests ADD CONSTRAINT production_requests_qty_requested_positive CHECK (qty_requested > 0)');
        DB::statement('ALTER TABLE purchase_order_items ADD CONSTRAINT purchase_order_items_qty_ordered_positive CHECK (qty_ordered > 0)');
        DB::statement('ALTER TABLE purchase_order_items ADD CONSTRAINT purchase_order_items_received_within_ordered CHECK (qty_received >= 0 AND qty_received <= qty_ordered)');

        // ---------- Unique index (IF NOT EXISTS: aman jika sudah ada dengan nama ini) ----------
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS orders_platform_order_no_unique ON orders (platform_id, platform_order_no)');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS product_materials_group_material_unique ON product_materials (group_id, raw_material_id)');

        // Mempercepat antrean produksi (pesanan dulu, lalu restock; FIFO).
        DB::statement('CREATE INDEX IF NOT EXISTS production_requests_queue_idx ON production_requests (status, source, created_at, id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS production_requests_queue_idx');
        DB::statement('DROP INDEX IF EXISTS product_materials_group_material_unique');
        DB::statement('DROP INDEX IF EXISTS orders_platform_order_no_unique');

        DB::statement('ALTER TABLE purchase_order_items DROP CONSTRAINT IF EXISTS purchase_order_items_received_within_ordered');
        DB::statement('ALTER TABLE purchase_order_items DROP CONSTRAINT IF EXISTS purchase_order_items_qty_ordered_positive');
        DB::statement('ALTER TABLE production_requests DROP CONSTRAINT IF EXISTS production_requests_qty_requested_positive');
        DB::statement('ALTER TABLE order_items DROP CONSTRAINT IF EXISTS order_items_qty_positive');

        DB::statement('ALTER TABLE raw_material_movements DROP CONSTRAINT IF EXISTS raw_material_movements_source_check');
        DB::statement('ALTER TABLE product_stock_movements DROP CONSTRAINT IF EXISTS product_stock_movements_source_check');
        DB::statement('ALTER TABLE raw_material_movements DROP CONSTRAINT IF EXISTS raw_material_movements_type_check');
        DB::statement('ALTER TABLE product_stock_movements DROP CONSTRAINT IF EXISTS product_stock_movements_type_check');
        DB::statement('ALTER TABLE production_requests DROP CONSTRAINT IF EXISTS production_requests_source_check');
        DB::statement('ALTER TABLE production_requests DROP CONSTRAINT IF EXISTS production_requests_status_check');
        DB::statement('ALTER TABLE raw_materials DROP CONSTRAINT IF EXISTS raw_materials_material_type_check');
        DB::statement('ALTER TABLE product_materials DROP CONSTRAINT IF EXISTS product_materials_usage_method_check');
        DB::statement('ALTER TABLE product_groups DROP CONSTRAINT IF EXISTS product_groups_production_method_check');
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
    }
};
