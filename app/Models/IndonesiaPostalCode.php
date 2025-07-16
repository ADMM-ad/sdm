<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndonesiaPostalCode extends Model
{
    use HasFactory;

    protected $table = 'indonesia_postal_codes'; // Pastikan sesuai dengan nama tabel migrasi

    protected $fillable = [
        'postal_code',
        'village',
        'district',
        'regency', // Ini akan menyimpan nama kota/kabupaten
        'province',
        'latitude',
        'longitude',
        'elevation',
        'timezone',
    ];
}
