<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedSmallInteger('lead_time_days')->default(0); // diisi Admin
            $table->decimal('min_order_qty', 12, 3)->default(0);
            $table->timestamps();

            $table->unique(['supplier_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_materials');
    }
};
