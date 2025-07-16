<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iklan extends Model
{
    use HasFactory;

    protected $table = 'iklan';

    protected $fillable = [
        'awal_pelaporan',
        'kode_kampanye_id',
        'hasil',
        'jumlah_dibelanjakan',
    ];

    // Relasi ke model Kampanye (kode_kampanye_cs)
    public function kampanye()
    {
        return $this->belongsTo(Kampanye::class, 'kode_kampanye_id');
    }
}
