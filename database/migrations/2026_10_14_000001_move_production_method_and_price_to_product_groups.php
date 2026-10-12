<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sudah dalam kondisi akhir (contoh: database baru yang memakai versi terbaru migration sebelumnya).
        if (Schema::hasTable('product_groups') && Schema::hasColumn('product_groups', 'production_method')) {
            return;
        }

        // Kondisi awal yang diharapkan: product_groups ada, products punya group_id, production_method, dan price.
        if (! Schema::hasTable('product_groups')
            || ! Schema::hasColumn('products', 'group_id')
            || ! Schema::hasColumn('products', 'production_method')
            || ! Schema::hasColumn('products', 'price')) {
            throw new RuntimeException('Struktur tabel tidak sesuai harapan. Cek kolom products dan product_groups di Supabase sebelum menjalankan migration ini.');
        }

        // Hentikan jika variasi dalam satu induk punya metode produksi atau harga yang berbeda,
        // karena nilainya tidak bisa digabung otomatis tanpa kehilangan informasi.
        $conflicts = DB::table('products')
            ->select('group_id')
            ->groupBy('group_id')
            ->havingRaw('COUNT(DISTINCT production_method) > 1 OR COUNT(DISTINCT price) > 1')
            ->pluck('group_id');

        if ($conflicts->isNotEmpty()) {
            throw new RuntimeException(
                'Variasi dengan metode produksi atau harga berbeda ditemukan pada product_groups id: '
                . $conflicts->implode(', ')
                . '. Samakan nilainya atau pecah menjadi induk terpisah, lalu jalankan ulang.'
            );
        }

        // 1. Tambah kolom di induk (sementara boleh kosong)
        Schema::table('product_groups', function (Blueprint $table) {
            $table->string('production_method', 3)->nullable()->after('category');
            $table->decimal('price', 15, 2)->default(0)->after('production_method');
        });

        // 2. Salin nilai dari variasi pertama tiap induk
        DB::statement('
            UPDATE product_groups g
            SET production_method = p.production_method,
                price = p.price
            FROM (
                SELECT DISTINCT ON (group_id) group_id, production_method, price
                FROM products
                ORDER BY group_id, id
            ) p
            WHERE g.id = p.group_id
        ');

        // Induk tanpa variasi: isi MTO sebagai nilai awal (periksa lagi secara manual jika ada).
        DB::table('product_groups')->whereNull('production_method')->update(['production_method' => 'MTO']);

        // 3. Jadikan wajib terisi dan beri index
        DB::statement('ALTER TABLE product_groups ALTER COLUMN production_method SET NOT NULL');
        Schema::table('product_groups', function (Blueprint $table) {
            $table->index('production_method');
        });

        // 4. Hapus dari products (index kolom ikut terhapus oleh PostgreSQL)
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['production_method', 'price']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('production_method', 3)->default('MTO');
            $table->decimal('price', 15, 2)->default(0);
        });

        DB::statement('
            UPDATE products p
            SET production_method = g.production_method,
                price = g.price
            FROM product_groups g
            WHERE g.id = p.group_id
        ');

        Schema::table('products', function (Blueprint $table) {
            $table->index('production_method');
        });

        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropIndex(['production_method']);
            $table->dropColumn(['production_method', 'price']);
        });
    }
};
