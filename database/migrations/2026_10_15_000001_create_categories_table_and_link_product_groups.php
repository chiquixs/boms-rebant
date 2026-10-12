<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // Pertahankan data: kategori yang sudah tertulis di product_groups dipindah ke tabel categories.
        $names = DB::table('product_groups')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($names as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('product_groups', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')
                ->constrained('categories')->restrictOnDelete();
        });

        DB::statement('
            UPDATE product_groups g
            SET category_id = c.id
            FROM categories c
            WHERE c.name = g.category
        ');

        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('product_groups', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('name');
        });

        DB::statement('
            UPDATE product_groups g
            SET category = c.name
            FROM categories c
            WHERE c.id = g.category_id
        ');

        Schema::table('product_groups', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
