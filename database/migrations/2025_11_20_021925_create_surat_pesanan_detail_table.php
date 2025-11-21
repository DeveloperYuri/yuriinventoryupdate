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
        Schema::create('surat_pesanan_atk_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('surat_pesanan_atk_header');
            $table->integer('atk_id');
            $table->integer('qty');
            $table->integer('stok');
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
