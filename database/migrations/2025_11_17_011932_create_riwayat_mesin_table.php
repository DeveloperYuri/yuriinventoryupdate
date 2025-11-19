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
        Schema::create('riwayat_mesin', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_mesin');
            $table->string('running_hour');
            $table->string('pekerjaan');
            $table->string('pic');
            $table->text('deskripsi');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_mesin');
    }
};
