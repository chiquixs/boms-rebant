<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hasil produksi aktual. Satu permintaan produksi boleh punya banyak log (juga dalam satu hari).
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_request_id')->constrained('production_requests');
            $table->foreignId('recorded_by')->constrained('users');
            $table->date('production_date');
            $table->unsignedInteger('qty_produced');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Dibutuhkan supaya production_log_materials bisa memakai FK komposit
            // (log dan request dijamin cocok).
            $table->unique(['id', 'production_request_id'], 'production_logs_id_request_unique');
            $table->index(['production_request_id', 'production_date'], 'production_logs_request_date_idx');
        });

        DB::statement('ALTER TABLE production_logs ADD CONSTRAINT production_logs_qty_positive CHECK (qty_produced > 0)');

        // Pemakaian bahan aktual. Aturan: satu (request, bahan, tanggal) hanya dicatat satu kali.
        Schema::create('production_log_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_log_id');
            $table->unsignedBigInteger('production_request_id');
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->date('usage_date');
            $table->decimal('qty_used', 15, 3);
            $table->timestamp('created_at')->useCurrent();

            // FK komposit: production_request_id di sini pasti sama dengan milik log-nya.
            $table->foreign(['production_log_id', 'production_request_id'], 'production_log_materials_log_request_foreign')
                ->references(['id', 'production_request_id'])
                ->on('production_logs');

            $table->unique(
                ['production_request_id', 'raw_material_id', 'usage_date'],
                'production_log_materials_one_per_day_unique'
            );
            $table->index('production_log_id');
            $table->index('raw_material_id');
        });

        DB::statement('ALTER TABLE production_log_materials ADD CONSTRAINT production_log_materials_qty_positive CHECK (qty_used > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('production_log_materials');
        Schema::dropIfExists('production_logs');
    }
};
