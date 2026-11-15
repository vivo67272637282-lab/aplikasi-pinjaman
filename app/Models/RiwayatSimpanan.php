<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class RiwayatSimpanan extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $table = 'riwayat_simpanans';

    protected $fillable = [
        'user_id',
        'karyawan_id',
        'periode_id',
        'jenis',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
