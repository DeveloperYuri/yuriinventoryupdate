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
        Schema::create('surat_pesanan_baru_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('surat_pesanan_baru_header_id');
            $table->enum('item_type', ['sparepart', 'atk', 'asset']);
            $table->integer('item_id');
            $table->integer('qty');
            $table->integer('qty_kurang');
            $table->integer('stock');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pesanan_baru_detail');
    }
};
