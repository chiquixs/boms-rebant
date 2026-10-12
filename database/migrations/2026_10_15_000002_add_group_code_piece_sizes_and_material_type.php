<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. product_groups.code = "sku product" (misalnya D6-A), unik, dipakai untuk mencocokkan resep.
        //    Baris yang sudah ada diberi kode sementara GRP-<id> agar data tidak hilang.
        Schema::table('product_groups', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->after('id');
        });
        DB::statement("UPDATE product_groups SET code = 'GRP-' || id WHERE code IS NULL");
        DB::statement('ALTER TABLE product_groups ALTER COLUMN code SET NOT NULL');
        Schema::table('product_groups', function (Blueprint $table) {
            $table->unique('code');
        });

        // 2. product_materials: ukuran potong per bahan (satu produk bisa punya beberapa bahan dengan ukuran berbeda).
        Schema::table('product_materials', function (Blueprint $table) {
            $table->decimal('piece_length_cm', 8, 2)->nullable()->after('usage_method');
            $table->decimal('piece_width_cm', 8, 2)->nullable()->after('piece_length_cm');
            $table->unsignedSmallInteger('piece_count')->default(1)->after('piece_width_cm'); // jumlah potongan per produk (set isi 3 = 3)
        });

        // Baris AREA yang sudah ada memakai ukuran induk sebelumnya, jadi ukuran itu disalin agar perilakunya tetap sama.
        DB::statement("
            UPDATE product_materials pm
            SET piece_length_cm = g.length_cm,
                piece_width_cm = g.width_cm
            FROM product_groups g
            WHERE g.id = pm.group_id
              AND pm.usage_method = 'AREA'
              AND pm.piece_length_cm IS NULL
        ");

        // Baris baru dengan metode AREA wajib punya ukuran potong (NOT VALID: baris lama tidak diperiksa ulang).
        DB::statement("
            ALTER TABLE product_materials
            ADD CONSTRAINT product_materials_area_requires_piece_size
            CHECK (usage_method <> 'AREA' OR (piece_length_cm IS NOT NULL AND piece_width_cm IS NOT NULL))
            NOT VALID
        ");

        DB::statement('ALTER TABLE product_materials ADD CONSTRAINT product_materials_piece_count_positive CHECK (piece_count >= 1)');

        // 3. raw_materials.material_type: nilai SHEET atau NON_SHEET (sebelumnya UNIT).
        DB::statement("UPDATE raw_materials SET material_type = 'NON_SHEET' WHERE material_type = 'UNIT'");
        DB::statement("ALTER TABLE raw_materials ALTER COLUMN material_type SET DEFAULT 'NON_SHEET'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE raw_materials ALTER COLUMN material_type SET DEFAULT 'UNIT'");
        DB::statement("UPDATE raw_materials SET material_type = 'UNIT' WHERE material_type = 'NON_SHEET'");

        DB::statement('ALTER TABLE product_materials DROP CONSTRAINT IF EXISTS product_materials_area_requires_piece_size');
        DB::statement('ALTER TABLE product_materials DROP CONSTRAINT IF EXISTS product_materials_piece_count_positive');
        Schema::table('product_materials', function (Blueprint $table) {
            $table->dropColumn(['piece_length_cm', 'piece_width_cm', 'piece_count']);
        });

        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
