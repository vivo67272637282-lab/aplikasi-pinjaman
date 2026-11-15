<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class Simpanan extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $fillable = [
        'user_id',
        'karyawan_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
        'total_simpanan',
    ];

    protected $casts = [
        'simpanan_pokok'     => 'decimal:2',
        'simpanan_wajib'     => 'decimal:2',
        'simpanan_sukarela'  => 'decimal:2',
        'total_simpanan'     => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function hitungTotal(): void
    {
        $this->total_simpanan = $this->simpanan_pokok + $this->simpanan_wajib + $this->simpanan_sukarela;
    }
}
