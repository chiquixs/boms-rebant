<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_material_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->decimal('qty_planned', 12, 3);
            $table->decimal('qty_used', 12, 3)->nullable(); // diinput Production, wajib terisi sebelum DONE
            $table->timestamps();

            $table->unique(['production_request_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_material_usages');
    }
};
