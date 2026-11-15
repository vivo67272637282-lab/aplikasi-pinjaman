<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCurrentUser;

class CompanySetting extends Model
{
    use BelongsToCurrentUser;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_address',
        'logo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
