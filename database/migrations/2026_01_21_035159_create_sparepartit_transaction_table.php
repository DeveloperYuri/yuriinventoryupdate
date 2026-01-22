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
        Schema::create('sparepartit_transaction', function (Blueprint $table) {
            $table->id();
            $table->integer('sparepartit_id');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('sparepartit_masuk_header_id');
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
        Schema::dropIfExists('sparepartit_transaction');
    }
};
