<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete(); // null jika dibuat sistem
            $table->string('request_no', 30)->unique();
            $table->string('source', 20);                    // ORDER atau MTS_MIN_STOCK
            $table->string('priority', 10)->default('NORMAL'); // NORMAL atau URGENT
            $table->unsignedInteger('qty_requested');
            $table->unsignedInteger('qty_produced')->default(0);
            $table->string('status', 20)->default('WAITING'); // WAITING, IN_PROCESS, DONE, CANCELLED
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['product_id', 'status']);
            $table->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_requests');
    }
};
