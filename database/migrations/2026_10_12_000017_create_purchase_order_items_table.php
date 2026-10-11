<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->foreignId('material_shortage_id')->nullable()->constrained('material_shortages')->nullOnDelete();
            $table->decimal('qty_suggested', 12, 3)->nullable(); // null pada PO manual
            $table->decimal('qty_ordered', 12, 3);               // awalnya sama dengan qty_suggested, bisa diubah Admin
            $table->decimal('qty_received', 12, 3)->default(0);
            $table->decimal('unit_price', 15, 2)->nullable();    // terisi setelah supplier dipilih
            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('raw_material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
