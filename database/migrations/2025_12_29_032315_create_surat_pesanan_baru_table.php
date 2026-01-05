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
        Schema::create('surat_pesanan_baru_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_pesanan');
            $table->string('name');
            $table->integer('department_id');
            $table->integer('locations_id');
            $table->integer('category_id')->nullable();
            $table->integer('subcategory_id')->nullable();
            $table->enum('status', ['draft', 'onprogress', 'approved', 'rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_pesanan_baru');
    }
};
