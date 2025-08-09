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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->float('hpp');
            $table->float('harga_jual');
            $table->string('detail_produk');
            $table->foreignId('jenis_produk_id')->nullable()->constrained('jenis_produk')->onDelete('set null');
            $table->foreignId('jenis_lead_id')->nullable()->constrained('jenis_lead')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
