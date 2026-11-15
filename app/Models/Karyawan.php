<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCurrentUser;

class Karyawan extends Model
{
    use BelongsToCurrentUser, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->nik)) {
                $model->nik = self::generateKodeKaryawan($model->user_id ?? auth()->id());
            }
        });
    }

    public static function generateKodeKaryawan(?int $userId = null): string
    {
        $userId = $userId ?? auth()->id();

        $latestKode = self::query()
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereNotNull('nik')
            ->where('nik', 'like', 'KK%')
            ->orderByDesc('id')
            ->value('nik');

        if (empty($latestKode)) {
            return 'KK001';
        }

        if (preg_match('/KK(\d+)/i', $latestKode, $matches)) {
            $nomor = (int) $matches[1] + 1;
            return 'KK' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
        }

        return 'KK001';
    }

    protected $fillable = [
        'user_id',
        'nik',
        'nama',
        'jabatan',
        'no_hp',
        'no_wa',
        'email',
        'alamat',
        'jenis_kelamin',
        'tanggal_masuk',
        'is_active',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'is_active'     => 'boolean',
    ];

    public function pinjamans()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function kasbons()
    {
        return $this->hasMany(Kasbon::class);
    }

    public function simpanan()
    {
        return $this->hasOne(Simpanan::class);
    }

    public function potongans()
    {
        return $this->hasMany(Potongan::class);
    }

    public function riwayatSimpanans()
    {
        return $this->hasMany(RiwayatSimpanan::class);
    }

    public function waLogs()
    {
        return $this->hasMany(WaLog::class);
    }

    // Hitung total sisa pinjaman aktif
    public function getSisaPinjamanAttribute(): float
    {
        return $this->pinjamans()
            ->where('status', 'disetujui')
            ->sum('sisa_pinjaman');
    }

    // Hitung total sisa kasbon aktif
    public function getSisaKasbonAttribute(): float
    {
        return $this->kasbons()
            ->where('status', 'disetujui')
            ->sum('sisa_kasbon');
    }
}
