<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaman: struktur lama (data produk di products, resep per SKU) diganti total.
        if (DB::table('products')->exists() || DB::table('product_materials')->exists()) {
            throw new RuntimeException(
                'Tabel products atau product_materials sudah berisi data. Kosongkan atau pindahkan data dulu sebelum menjalankan migration ini.'
            );
        }

        // 1. Produk induk: nama, kategori, metode produksi, harga, ukuran, foto utama
        Schema::create('product_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('category', 100)->nullable();
            $table->string('production_method', 3); // MTS atau MTO, berlaku untuk semua variasi
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // foto utama
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('production_method');
        });

        // 2. products menjadi tingkat variasi (satu baris per SKU): stok dan foto per variasi
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['production_method']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('group_id')->after('id')->constrained('product_groups')->restrictOnDelete();
            $table->string('variant_name', 100)->nullable()->after('sku'); // contoh: Hitam, Putih, Pink
            $table->string('image_path')->nullable()->after('variant_name'); // foto khusus variasi (opsional)
            $table->unique(['group_id', 'variant_name']); // cegah variasi ganda dalam satu induk
            $table->dropColumn(['name', 'category', 'production_method', 'price', 'length_cm', 'width_cm']);
        });

        // 3. Resep per produk induk (dipakai semua variasinya)
        Schema::dropIfExists('product_materials');
        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('product_groups')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->string('usage_method', 10)->default('AREA'); // AREA atau FIXED
            $table->decimal('waste_percent', 5, 2)->default(0);
            $table->decimal('qty_per_unit', 12, 6);
            $table->timestamps();

            $table->unique(['group_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_materials');
        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->string('usage_method', 10)->default('AREA');
            $table->decimal('waste_percent', 5, 2)->default(0);
            $table->decimal('qty_per_unit', 12, 6);
            $table->timestamps();

            $table->unique(['product_id', 'raw_material_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'variant_name']);
            $table->dropConstrainedForeignId('group_id');
            $table->dropColumn(['variant_name', 'image_path']);
            $table->string('name', 150)->default('');
            $table->string('category', 100)->nullable();
            $table->string('production_method', 3)->default('MTO');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->index('production_method');
        });

        Schema::dropIfExists('product_groups');
    }
};