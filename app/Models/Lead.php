<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penjualan;
class Lead extends Model
{
    use HasFactory;

    protected $table = 'lead'; // Nama tabel

    protected $fillable = [
        'user_id',
        'jumlah_lead',
        'tanggal',
         'jenis_lead_id',
    ];

    // Relasi: Lead dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

  public function penjualanTerkait()
{
    return Penjualan::whereColumn('penjualan.tanggal', 'leads.tanggal')
                    ->whereColumn('penjualan.id_user', 'leads.user_id');
}

public function jenisLead()
{
    return $this->belongsTo(JenisLead::class, 'jenis_lead_id');
}


}
