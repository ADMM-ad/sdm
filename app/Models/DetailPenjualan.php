<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
protected $table = 'detail_penjualan';

    protected $fillable = [
    'id_penjualan', 'id_produk', 'jumlah', 'total_harga','nama_variasi','hasil_pembagian_omset', 'hasil_pembagian_ongkir','hasil_pembagian_biayacod','hasil_pembagian_cashback',
];

public function produk() {
    return $this->belongsTo(Produk::class, 'id_produk');
}

public function penjualan()
{
    return $this->belongsTo(Penjualan::class, 'id_penjualan');
}
}
