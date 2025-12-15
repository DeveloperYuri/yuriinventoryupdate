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
        Schema::create('surat_permintaan_spare_part_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('surat_permintaan_spare_part_header_id');
            $table->integer('spare_part_id');
            $table->integer('qty');
            $table->integer('stock');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_permintaan_detail');
    }
};
