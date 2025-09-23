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
        Schema::table('surat_pesanan_header', function (Blueprint $table) {
            $table->enum('status', ['draft', 'onprogress', 'approved', 'rejected'])
                  ->default('draft')
                  ->after('subcategory_id'); // taruh setelah kolom subcategory_id
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
