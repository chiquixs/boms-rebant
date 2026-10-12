<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Satu item pesanan hanya boleh punya satu production request yang masih aktif.
        DB::statement("
            CREATE UNIQUE INDEX production_requests_one_active_per_order_item
            ON production_requests (order_item_id)
            WHERE order_item_id IS NOT NULL AND status IN ('WAITING', 'IN_PROCESS')
        ");

        // Stok produk jadi untuk satu item pesanan hanya boleh dikurangi satu kali.
        // Kode aplikasi mencatat OUT dengan reference_type = 'order_item' dan reference_id = id item pesanan.
        DB::statement("
            CREATE UNIQUE INDEX product_stock_movements_one_out_per_order_item
            ON product_stock_movements (reference_id)
            WHERE movement_type = 'OUT' AND reference_type = 'order_item'
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS product_stock_movements_one_out_per_order_item');
        DB::statement('DROP INDEX IF EXISTS production_requests_one_active_per_order_item');
    }
};
