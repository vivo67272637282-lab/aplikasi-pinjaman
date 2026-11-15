<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToCurrentUser;

class Kasbon extends Model
{
    use BelongsToCurrentUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'no_kasbon',
        'karyawan_id',
        'jumlah_kasbon',
        'sisa_kasbon',
        'tanggal_kasbon',
        'keperluan',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kasbon'    => 'date',
        'tanggal_disetujui' => 'datetime',
        'jumlah_kasbon'     => 'decimal:2',
        'sisa_kasbon'       => 'decimal:2',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public static function generateNomor(): string
    {
        $prefix = 'KSB-' . date('Ym') . '-';
        $last = static::where('no_kasbon', 'like', $prefix . '%')
            ->orderBy('no_kasbon', 'desc')
            ->first();
        $seq = $last ? (int) substr($last->no_kasbon, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
