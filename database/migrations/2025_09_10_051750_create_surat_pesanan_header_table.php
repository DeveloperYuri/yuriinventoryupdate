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
        Schema::create('surat_pesanan_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_pesanan')->unique(); // nomor surat pesanan
            $table->string('name'); // nama pemesan

            // Foreign keys
            $table->foreignId('locations_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('category')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('subcategory')->cascadeOnDelete();
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
