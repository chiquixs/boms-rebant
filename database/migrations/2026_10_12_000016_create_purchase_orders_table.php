<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->restrictOnDelete(); // null sampai Admin memilih supplier
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // null jika otomatis
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('po_no', 30)->unique();
            $table->string('trigger_type', 20); // AUTO_REORDER, SHORTAGE, MANUAL
            $table->string('status', 20)->default('PENDING_APPROVAL'); // PENDING_APPROVAL, SENT, REJECTED, RECEIVED
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
