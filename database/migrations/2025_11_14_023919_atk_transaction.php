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
            $table->integer('atk_id');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('atk_masuk_header_id');
            $table->integer('atk_keluar_header_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
