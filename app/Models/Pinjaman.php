<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCurrentUser;

class Pinjaman extends Model
{
    use BelongsToCurrentUser, HasFactory, SoftDeletes;

    /**
     * Explicit table name to avoid incorrect pluralization.
     */
    protected $table = 'pinjamans';

    protected $fillable = [
        'user_id',
        'no_pinjaman',
        'karyawan_id',
        'jumlah_pinjaman',
        'bunga_persen',
        'tenor',
        'cicilan_per_bulan',
        'sisa_pinjaman',
        'tanggal_pinjaman',
        'keperluan',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pinjaman'  => 'date',
        'tanggal_disetujui' => 'datetime',
        'jumlah_pinjaman'   => 'decimal:2',
        'bunga_persen'      => 'decimal:2',
        'cicilan_per_bulan' => 'decimal:2',
        'sisa_pinjaman'     => 'decimal:2',
    ];

    public function getJumlahBungaAttribute(): float
    {
        return round((float) $this->jumlah_pinjaman * (float) $this->bunga_persen / 100, 2);
    }

    public function getTotalPengembalianAttribute(): float
    {
        return (float) $this->jumlah_pinjaman + $this->jumlah_bunga;
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pembayarans()
    {
        return $this->hasMany(PinjamanPembayaran::class)->orderBy('pembayaran_ke');
    }

    public function getJumlahPembayaranAttribute(): int
    {
        return $this->pembayarans()->count();
    }

    public function getPembayaranBerikutnyaAttribute(): ?int
    {
        return $this->sisa_pinjaman > 0
            ? $this->jumlah_pembayaran + 1
            : null;
    }

    public function getJumlahPembayaranBerikutnyaAttribute(): ?float
    {
        if (!$this->pembayaran_berikutnya) {
            return null;
        }

        if ($this->pembayaran_berikutnya >= $this->tenor) {
            return (float) $this->sisa_pinjaman;
        }

        return min((float) $this->cicilan_per_bulan, (float) $this->sisa_pinjaman);
    }

    public static function generateNomor(): string
    {
        $prefix = 'PIN-' . date('Ym') . '-';
        $last = static::where('no_pinjaman', 'like', $prefix . '%')
            ->orderBy('no_pinjaman', 'desc')
            ->first();
        $seq = $last ? (int) substr($last->no_pinjaman, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
