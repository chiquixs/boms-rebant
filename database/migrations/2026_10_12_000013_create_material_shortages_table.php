<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_shortages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->decimal('qty_needed', 12, 3);
            $table->decimal('qty_available', 12, 3);
            $table->string('status', 20)->default('OPEN'); // OPEN atau RESOLVED
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_shortages');
    }
};
