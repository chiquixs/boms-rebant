<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Waktu pesanan masuk READY_TO_PACK dan COMPLETED.
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('ready_at')->nullable()->after('notes');
            $table->timestamp('completed_at')->nullable()->after('ready_at');
        });

        // Kekurangan bahan yang sudah terselesaikan.
        Schema::table('material_shortages', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->after('reported_at');
        });

        // Supplier nonaktif tidak muncul di pilihan PO, tapi riwayatnya tetap ada.
        Schema::table('suppliers', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('address');
        });

        // Detail perubahan sebelum/sesudah.
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->jsonb('metadata')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', fn (Blueprint $t) => $t->dropColumn('metadata'));
        Schema::table('suppliers', fn (Blueprint $t) => $t->dropColumn('is_active'));
        Schema::table('material_shortages', fn (Blueprint $t) => $t->dropColumn('resolved_at'));
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['ready_at', 'completed_at']));
    }
};
