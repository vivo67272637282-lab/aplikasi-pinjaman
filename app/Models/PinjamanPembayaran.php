<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanPembayaran extends Model
{
    protected $fillable = [
        'pembayaran_ke',
        'jumlah_bayar',
        'tanggal_bayar',
    ];

    protected $casts = [
        'pembayaran_ke' => 'integer',
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
