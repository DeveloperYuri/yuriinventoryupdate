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
        Schema::create('surat_pesanan_atk_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_pesanan');
            $table->string('dibuat_oleh');
            $table->integer('locations_id');
            $table->enum('status', ['draft', 'onprogress', 'reject', 'approve'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pesanan_header');
    }
};
