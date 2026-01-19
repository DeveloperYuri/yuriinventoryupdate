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
        Schema::create('asset_it', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('nomer_asset');
            $table->string('nama');
            $table->string('user')->nullable();
            $table->integer('locations_id');
            $table->text('spesifikasi')->nullable();
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_it');
    }
};
