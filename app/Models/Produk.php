<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'hpp',
        'harga_jual',
        'detail_produk',
        'jenis_produk_id',
         'jenis_lead_id',
    ];

public function jenisProduk()
{
    return $this->belongsTo(JenisProduk::class, 'jenis_produk_id');
}


    public function jenisLead()
{
    return $this->belongsTo(JenisLead::class, 'jenis_lead_id');
}
}
