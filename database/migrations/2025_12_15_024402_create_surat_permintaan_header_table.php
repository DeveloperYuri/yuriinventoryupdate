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
        Schema::create('surat_permintaan_spare_part_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_permintaan');
            $table->string('name');
            $table->integer('locations_id');
            $table->integer('category_id');
            $table->integer('subcategory_id');
            $table->enum('status', ['draft', 'onprogress', 'approved', 'rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_permintaan_header');
    }
};
