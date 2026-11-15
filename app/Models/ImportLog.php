<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class ImportLog extends Model
{
    use BelongsToCurrentUser, HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'tipe',
        'total_baris',
        'berhasil',
        'gagal',
        'pesan_error',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
