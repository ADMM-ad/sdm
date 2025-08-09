<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kampanye extends Model
{
    use HasFactory;

    protected $table = 'kode_kampanye_cs'; // nama tabel

    protected $fillable = [
        'user_id',
        'kode_kampanye',
        'jenis_lead_id',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function jenisLead()
    {
    return $this->belongsTo(JenisLead::class, 'jenis_lead_id');
    }

}
