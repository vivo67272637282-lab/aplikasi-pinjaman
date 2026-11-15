<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function pinjamanDisetujui()
    {
        return $this->hasMany(Pinjaman::class, 'disetujui_oleh');
    }

    public function kasbonDisetujui()
    {
        return $this->hasMany(Kasbon::class, 'disetujui_oleh');
    }

    public function importLogs()
    {
        return $this->hasMany(ImportLog::class);
    }

    public function waLogs()
    {
        return $this->hasMany(WaLog::class);
    }
}
