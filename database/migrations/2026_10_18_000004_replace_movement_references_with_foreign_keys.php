<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Data saat ini (hasil pemeriksaan): seluruh riwayat lama adalah reference_type = 'opening_stock'
     * dengan movement_type = 'ADJUSTMENT' (27 baris produk, 7 baris bahan).
     *
     * Stok awal adalah penyesuaian tanpa dokumen sumber, jadi tidak punya FK. Pergerakan ADJUSTMENT
     * boleh tanpa referensi; jejaknya tetap ada di created_by, qty_before/qty_after, note, created_at.
     * Penanda 'opening_stock' dipindahkan ke kolom note agar maknanya tidak hilang.
     */
    public function up(): void
    {
        // Pengaman: reference_id pada opening_stock harus kosong atau sama dengan id produk/bahannya,
        // supaya tidak ada informasi yang hilang saat kolom lama dihapus.
        foreach ([['product_stock_movements', 'product_id'], ['raw_material_movements', 'raw_material_id']] as [$tbl, $col]) {
            $odd = DB::table($tbl)
                ->where('reference_type', 'opening_stock')
                ->whereNotNull('reference_id')
                ->whereColumn('reference_id', '<>', $col)
                ->count();

            if ($odd > 0) {
                throw new RuntimeException(
                    "{$tbl}: ada {$odd} baris opening_stock dengan reference_id yang bukan {$col}. Periksa dulu sebelum kolom lama dihapus."
                );
            }

            DB::statement("
                UPDATE {$tbl}
                SET note = 'Stok awal (opening_stock)'
                WHERE reference_type = 'opening_stock' AND (note IS NULL OR note = '')
            ");
        }

        /*
         * ---------- product_stock_movements ----------
         */
        Schema::table('product_stock_movements', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->after('created_by')->constrained('order_items');
            $table->foreignId('production_log_id')->nullable()->after('order_item_id')->constrained('production_logs');
        });

        DB::statement("
            UPDATE product_stock_movements m
            SET order_item_id = m.reference_id
            FROM order_items oi
            WHERE m.reference_type = 'order_item'
              AND oi.id = m.reference_id
        ");

        // Referensi selain 'opening_stock' yang belum terpetakan = hentikan migrasi, jangan hilangkan data.
        $lost = DB::table('product_stock_movements')
            ->whereNotNull('reference_type')
            ->where('reference_type', '<>', 'opening_stock')
            ->whereNull('order_item_id')
            ->whereNull('production_log_id')
            ->count();

        if ($lost > 0) {
            throw new RuntimeException(
                "Ada {$lost} baris product_stock_movements dengan reference_type yang belum bisa dipetakan. Petakan manual dulu."
            );
        }

        // Index pencegah Stock OUT ganda dibuat ulang di kolom baru (nama sama dengan index lama).
        DB::statement('DROP INDEX IF EXISTS product_stock_movements_one_out_per_order_item');
        DB::statement("
            CREATE UNIQUE INDEX product_stock_movements_one_out_per_order_item
            ON product_stock_movements (order_item_id)
            WHERE movement_type = 'OUT' AND order_item_id IS NOT NULL
        ");

        // Satu log produksi MTS hanya boleh menghasilkan satu pergerakan stok.
        DB::statement("
            CREATE UNIQUE INDEX product_stock_movements_one_per_production_log
            ON product_stock_movements (production_log_id)
            WHERE production_log_id IS NOT NULL
        ");

        // Satu pergerakan menunjuk paling banyak satu sumber (ADJUSTMENT: tanpa sumber).
        DB::statement('
            ALTER TABLE product_stock_movements
            ADD CONSTRAINT product_stock_movements_single_reference
            CHECK (num_nonnulls(order_item_id, production_log_id) <= 1)
        ');

        Schema::table('product_stock_movements', function (Blueprint $table) {
            $table->index('order_item_id');
            $table->dropColumn(['reference_type', 'reference_id']);
        });

        /*
         * ---------- raw_material_movements ----------
         */
        Schema::table('raw_material_movements', function (Blueprint $table) {
            $table->foreignId('production_log_material_id')->nullable()->after('created_by')
                ->constrained('production_log_materials');
            $table->foreignId('purchase_order_item_id')->nullable()->after('production_log_material_id')
                ->constrained('purchase_order_items');
        });

        DB::statement("
            UPDATE raw_material_movements m
            SET purchase_order_item_id = m.reference_id
            FROM purchase_order_items poi
            WHERE m.reference_type = 'purchase_order_item'
              AND poi.id = m.reference_id
        ");

        $lostRaw = DB::table('raw_material_movements')
            ->whereNotNull('reference_type')
            ->where('reference_type', '<>', 'opening_stock')
            ->whereNull('production_log_material_id')
            ->whereNull('purchase_order_item_id')
            ->count();

        if ($lostRaw > 0) {
            throw new RuntimeException(
                "Ada {$lostRaw} baris raw_material_movements dengan reference_type yang belum bisa dipetakan. Petakan manual dulu."
            );
        }

        // Satu pencatatan pemakaian bahan menghasilkan satu pergerakan stok.
        DB::statement("
            CREATE UNIQUE INDEX raw_material_movements_one_per_log_material
            ON raw_material_movements (production_log_material_id)
            WHERE production_log_material_id IS NOT NULL
        ");

        DB::statement('
            ALTER TABLE raw_material_movements
            ADD CONSTRAINT raw_material_movements_single_reference
            CHECK (num_nonnulls(production_log_material_id, purchase_order_item_id) <= 1)
        ');

        Schema::table('raw_material_movements', function (Blueprint $table) {
            $table->index('purchase_order_item_id');
            $table->dropColumn(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        // Rollback tidak sepenuhnya identik: reference_id lama untuk opening_stock tidak dipulihkan.
        /*
         * ---------- raw_material_movements ----------
         */
        Schema::table('raw_material_movements', function (Blueprint $table) {
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
        });

        DB::statement("
            UPDATE raw_material_movements
            SET reference_type = 'purchase_order_item', reference_id = purchase_order_item_id
            WHERE purchase_order_item_id IS NOT NULL
        ");
        DB::statement("
            UPDATE raw_material_movements
            SET reference_type = 'production_log_material', reference_id = production_log_material_id
            WHERE production_log_material_id IS NOT NULL
        ");
        DB::statement("
            UPDATE raw_material_movements
            SET reference_type = 'opening_stock'
            WHERE reference_type IS NULL AND note = 'Stok awal (opening_stock)'
        ");

        DB::statement('ALTER TABLE raw_material_movements DROP CONSTRAINT IF EXISTS raw_material_movements_single_reference');
        DB::statement('DROP INDEX IF EXISTS raw_material_movements_one_per_log_material');

        Schema::table('raw_material_movements', function (Blueprint $table) {
            $table->dropForeign(['production_log_material_id']);
            $table->dropForeign(['purchase_order_item_id']);
            $table->dropColumn(['production_log_material_id', 'purchase_order_item_id']);
        });

        /*
         * ---------- product_stock_movements ----------
         */
        Schema::table('product_stock_movements', function (Blueprint $table) {
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
        });

        DB::statement("
            UPDATE product_stock_movements
            SET reference_type = 'order_item', reference_id = order_item_id
            WHERE order_item_id IS NOT NULL
        ");
        DB::statement("
            UPDATE product_stock_movements
            SET reference_type = 'production_log', reference_id = production_log_id
            WHERE production_log_id IS NOT NULL
        ");
        DB::statement("
            UPDATE product_stock_movements
            SET reference_type = 'opening_stock'
            WHERE reference_type IS NULL AND note = 'Stok awal (opening_stock)'
        ");

        DB::statement('ALTER TABLE product_stock_movements DROP CONSTRAINT IF EXISTS product_stock_movements_single_reference');
        DB::statement('DROP INDEX IF EXISTS product_stock_movements_one_per_production_log');
        DB::statement('DROP INDEX IF EXISTS product_stock_movements_one_out_per_order_item');

        Schema::table('product_stock_movements', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropForeign(['production_log_id']);
            $table->dropColumn(['order_item_id', 'production_log_id']);
        });

        // Index versi lama (migrasi add_integrity_indexes).
        DB::statement("
            CREATE UNIQUE INDEX product_stock_movements_one_out_per_order_item
            ON product_stock_movements (reference_id)
            WHERE movement_type = 'OUT' AND reference_type = 'order_item'
        ");
    }
};
