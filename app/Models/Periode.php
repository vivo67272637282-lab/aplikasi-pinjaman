<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class Periode extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'bulan',
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function potongans()
    {
        return $this->hasMany(Potongan::class);
    }

    public function riwayatSimpanans()
    {
        return $this->hasMany(RiwayatSimpanan::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return ($bulanNama[$this->bulan] ?? $this->bulan) . ' ' . $this->tahun;
    }

    public static function aktif(): ?self
    {
        return static::where('status', 'aktif')->first();
    }
}
