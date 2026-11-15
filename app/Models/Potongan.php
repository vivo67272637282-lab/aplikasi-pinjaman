<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class Potongan extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $fillable = [
        'user_id',
        'karyawan_id',
        'periode_id',
        'potongan_pinjaman',
        'potongan_kasbon',
        'potongan_simpanan_wajib',
        'potongan_simpanan_sukarela',
        'potongan_lainnya',
        'keterangan_lainnya',
        'total_potongan',
        'catatan',
    ];

    protected $casts = [
        'potongan_pinjaman'         => 'decimal:2',
        'potongan_kasbon'           => 'decimal:2',
        'potongan_simpanan_wajib'   => 'decimal:2',
        'potongan_simpanan_sukarela'=> 'decimal:2',
        'potongan_lainnya'          => 'decimal:2',
        'total_potongan'            => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function hitungTotal(): void
    {
        $this->total_potongan = $this->potongan_pinjaman
            + $this->potongan_kasbon
            + $this->potongan_simpanan_wajib
            + $this->potongan_simpanan_sukarela
            + $this->potongan_lainnya;
    }
}
