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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->dateTime('tanggal');
            $table->string('order_id')->nullable();
            $table->string('metode_pengiriman');
            $table->string('metode_pembayaran');
            $table->string('nama_pembeli');
            $table->string('no_hp');
            $table->text('alamat');
            $table->string('kodepos');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->string('wilayah');
            $table->string('bukti')->nullable(); 
            $table->integer('ongkir');
            $table->text('detail')->nullable(); 
            $table->text('catatan')->nullable();
            $table->integer('total_bayar')->nullable();
            $table->integer('total_hpp')->nullable();
            $table->integer('dp')->nullable();
            $table->integer('cashback')->nullable(); 
            $table->integer('biaya_cod')->nullable();
            $table->string('kurir')->nullable();
            $table->string('no_resi')->nullable();
            $table->string('status_pesanan')->nullable();
            $table->string('alasan_batal')->nullable();
            $table->string('status_pembatalan')->nullable();
            $table->string('catatan_penjual')->nullable();
            $table->enum('kunci_hpp', ['ya', 'tidak'])->nullable();
            $table->timestamps();
        });

        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produk')->onDelete('cascade');
            $table->foreignId('id_penjualan')->constrained('penjualan')->onDelete('cascade');
            $table->integer('jumlah'); 
            $table->integer('total_harga');
            $table->string('nama_variasi')->nullable();
            $table->double('hasil_pembagian_omset')->nullable();
            $table->double('hasil_pembagian_ongkir')->nullable();  
            $table->double('hasil_pembagian_biayacod')->nullable();  
            $table->double('hasil_pembagian_cashback')->nullable();    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('detail_penjualan');
    }
};
