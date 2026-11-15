<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class WaLog extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $fillable = [
        'karyawan_id',
        'no_tujuan',
        'pesan',
        'status',
        'response',
        'tipe',
        'user_id',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
