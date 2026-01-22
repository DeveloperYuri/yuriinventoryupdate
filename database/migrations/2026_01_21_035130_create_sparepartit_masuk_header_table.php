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
        Schema::create('sparepartit_masuk_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_dokumen');
            $table->string('diterima_dari');
            $table->string('diterima_oleh');
            $table->date('tanggal');
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepartit_masuk_header');
    }
};
