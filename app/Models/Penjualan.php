<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
    'id_user',
    'tanggal',
    'order_id',              
    'metode_pengiriman',
    'metode_pembayaran',
    'nama_pembeli',
    'no_hp',
    'alamat',
    'kodepos',
    'provinsi',
    'kota',
    'kecamatan',
    'wilayah',
    'bukti',
    'ongkir',
    'detail',
    'catatan',
    'total_bayar',
    'total_hpp',
    'dp',
    'cashback',
    'biaya_cod',
    'kurir',
    'no_resi',
    'status_pesanan',
    'alasan_batal',
    'status_pembatalan',
    'catatan_penjual',
    'kunci_hpp',
];



    public function detailPenjualan()
{
    return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
}

public function user()
{
    return $this->belongsTo(User::class, 'id_user');
}

public function jobdeskEditor()
{
    return $this->hasOne(Editor::class, 'penjualan_id');
}

}
