<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_pesanan_detail', function (Blueprint $table) {
            $table->id();
             // Foreign key ke header baru
            $table->foreignId('surat_pesanan_header_id')
                  ->constrained('surat_pesanan_header')
                  ->cascadeOnDelete();

            // Foreign key ke spare part
            $table->foreignId('spare_part_id')
                  ->constrained('spare_parts')
                  ->cascadeOnDelete();

            $table->integer('qty')->default(0);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pesanan_detail');
    }
};
