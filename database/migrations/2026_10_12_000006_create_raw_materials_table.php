<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('unit', 20); // lembar, kg, pcs, dll.
            $table->string('material_type', 10)->default('UNIT'); // SHEET atau UNIT
            $table->decimal('sheet_length_cm', 8, 2)->nullable();
            $table->decimal('sheet_width_cm', 8, 2)->nullable();
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('safety_stock', 12, 3)->default(0);
            $table->decimal('target_stock', 12, 3)->default(0);
            $table->decimal('avg_daily_usage', 12, 3)->default(0); // cache, dihitung job harian
            $table->decimal('reorder_point', 12, 3)->default(0);   // cache, dihitung job harian
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE raw_materials ADD CONSTRAINT raw_materials_current_stock_non_negative CHECK (current_stock >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
