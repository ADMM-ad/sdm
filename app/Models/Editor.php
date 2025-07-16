<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Editor extends Model
{
    use HasFactory;

    protected $table = 'jobdesk_editor'; // Nama tabel eksplisit

    protected $fillable = [
        'user_id',
        'penjualan_id',
        'status',
    ];

     /**
     * Relasi ke model User (editor)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model DetailPenjualan
     */
    public function detailPenjualan()
    {
        return $this->belongsTo(DetailPenjualan::class, 'detail_penjualan_id');
    }

    public function penjualan()
{
    return $this->belongsTo(Penjualan::class);
}

}
