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
        Schema::create('atk_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_dokumen');
            $table->string('diterima_dari');
            $table->string('diterima_oleh');
            $table->integer('supplier_id');
            $table->string('sp_number');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atkmasuk_models');
    }
};
