<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 100)->nullable();
            $table->string('production_method', 3); // MTS atau MTO
            $table->decimal('length_cm', 8, 2)->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('current_stock')->default(0); // available stock, hanya diubah lewat transaksi + movement
            $table->unsignedInteger('min_stock')->nullable();     // hanya MTS
            $table->unsignedInteger('target_stock')->nullable();  // hanya MTS
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('production_method');
        });

        // PostgreSQL tidak punya tipe unsigned, jadi dijaga dengan CHECK constraint.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT products_current_stock_non_negative CHECK (current_stock >= 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT products_target_gt_min CHECK (min_stock IS NULL OR target_stock IS NULL OR target_stock > min_stock)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
