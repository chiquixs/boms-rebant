<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Antrean produksi tidak memakai priority: pesanan dulu, restock MTS kemudian,
    // di dalam tiap kelompok berurutan FIFO (created_at, id).
    //
    // JALANKAN TERAKHIR, dan hanya setelah pencarian 'priority' di app/, database/, resources/, routes/
    // tidak menemukan pemakaian lagi. Rollback membuat ulang kolom dengan default NORMAL
    // (nilai lama per baris tidak dipulihkan).
    public function up(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }

    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            $table->string('priority')->default('NORMAL')->after('source');
        });
    }
};
