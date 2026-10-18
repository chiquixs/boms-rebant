<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Alokasi (reservasi) stok produk jadi MTS per item pesanan.
     *
     *  RESERVED : stok dicadangkan untuk item ini, belum dikurangi dari products.current_stock
     *  CONSUMED : pesanan masuk READY_TO_PACK, stok sudah dikurangi (Stock OUT tercatat)
     *
     * Stok tersedia = products.current_stock - SUM(qty_allocated WHERE status = 'RESERVED')
     *
     * Hanya untuk item MTS. Satu item pesanan punya paling banyak satu baris.
     * qty_allocated boleh lebih kecil dari qty item; sisanya dipenuhi lewat production_requests.
     *
     * Batas qty_allocated <= order_items.qty tidak bisa dijamin CHECK biasa (lintas tabel),
     * jadi dijaga di service dalam transaksi dengan lockForUpdate.
     */
    public function up(): void
    {
        // Dibutuhkan agar alokasi bisa memakai FK komposit (produk pasti sama dengan produk item pesanan).
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_id_product_unique UNIQUE (id, product_id)');

        Schema::create('order_item_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id')->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty_allocated');
            $table->string('status', 20)->default('RESERVED');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->foreign(['order_item_id', 'product_id'], 'order_item_allocations_item_product_foreign')
                ->references(['id', 'product_id'])
                ->on('order_items');
        });

        DB::statement('ALTER TABLE order_item_allocations ADD CONSTRAINT order_item_allocations_qty_positive CHECK (qty_allocated > 0)');

        DB::statement("
            ALTER TABLE order_item_allocations ADD CONSTRAINT order_item_allocations_status_check
            CHECK (status IN ('RESERVED', 'CONSUMED'))
        ");

        DB::statement("
            ALTER TABLE order_item_allocations ADD CONSTRAINT order_item_allocations_consumed_at_check
            CHECK (status <> 'CONSUMED' OR consumed_at IS NOT NULL)
        ");

        // Mempercepat penghitungan stok tersedia per produk.
        DB::statement("
            CREATE INDEX order_item_allocations_reserved_idx
            ON order_item_allocations (product_id)
            WHERE status = 'RESERVED'
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_allocations');
        DB::statement('ALTER TABLE order_items DROP CONSTRAINT IF EXISTS order_items_id_product_unique');
    }
};
