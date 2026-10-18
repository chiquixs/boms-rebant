<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom (nullable dulu agar baris lama tidak gagal).
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('sku_snapshot', 50)->nullable()->after('product_id');
            $table->string('product_name_snapshot', 255)->nullable()->after('sku_snapshot');
            $table->string('production_method', 10)->nullable()->after('product_name_snapshot');
        });

        // 2. Backfill pesanan lama dari data master saat ini.
        DB::statement("
            UPDATE order_items oi
            SET sku_snapshot = p.sku,
                product_name_snapshot = CASE
                    WHEN p.variant_name IS NULL OR p.variant_name = '' THEN g.name
                    ELSE g.name || ' - ' || p.variant_name
                END,
                production_method = UPPER(g.production_method)
            FROM products p
            JOIN product_groups g ON g.id = p.group_id
            WHERE p.id = oi.product_id
              AND oi.sku_snapshot IS NULL
        ");

        // 3. Setelah terisi semua, jadikan wajib.
        DB::statement('ALTER TABLE order_items ALTER COLUMN sku_snapshot SET NOT NULL');
        DB::statement('ALTER TABLE order_items ALTER COLUMN product_name_snapshot SET NOT NULL');
        DB::statement('ALTER TABLE order_items ALTER COLUMN production_method SET NOT NULL');

        DB::statement("
            ALTER TABLE order_items
            ADD CONSTRAINT order_items_production_method_check
            CHECK (production_method IN ('MTS', 'MTO'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE order_items DROP CONSTRAINT IF EXISTS order_items_production_method_check');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['sku_snapshot', 'product_name_snapshot', 'production_method']);
        });
    }
};
