<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Izinkan beberapa sesi pemakaian bahan di hari yang sama untuk satu production request,
     * cukup batasi 1 baris per bahan untuk setiap sesi (production_log_id).
     */
    public function up(): void
    {
        Schema::table('production_log_materials', function (Blueprint $table): void {
            $table->dropUnique('production_log_materials_one_per_day_unique');
            $table->unique(['production_log_id', 'raw_material_id'], 'production_log_materials_one_per_log_unique');
        });
    }

    /**
     * Kembalikan unique index per request/bahan/tanggal.
     */
    public function down(): void
    {
        Schema::table('production_log_materials', function (Blueprint $table): void {
            $table->dropUnique('production_log_materials_one_per_log_unique');
            $table->unique(['production_request_id', 'raw_material_id', 'usage_date'], 'production_log_materials_one_per_day_unique');
        });
    }
};
