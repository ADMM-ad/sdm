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
       Schema::create('iklan', function (Blueprint $table) {
    $table->id();
    $table->date('awal_pelaporan');
    $table->foreignId('kode_kampanye_id')->constrained('kode_kampanye_cs')->onDelete('cascade');
    $table->integer('hasil');
    $table->integer('jumlah_dibelanjakan');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iklan');
    }
};
