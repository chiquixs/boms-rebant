<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel riwayat: tidak boleh diedit, jadi hanya created_at.
        // Konvensi qty: IN/OUT selalu positif, ADJUSTMENT boleh negatif. qty_before/qty_after menyimpan hasil akhirnya.
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('movement_type', 15); // IN, OUT, ADJUSTMENT
            $table->integer('qty');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->string('reference_type', 50)->nullable(); // relasi polimorfik, sengaja tanpa foreign key
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_movements');
    }
};
