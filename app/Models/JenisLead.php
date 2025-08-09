<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLead extends Model
{
    use HasFactory;

    protected $table = 'jenis_lead'; // Nama tabel yang digunakan

    protected $fillable = [
        'jenis',
    ];
}
