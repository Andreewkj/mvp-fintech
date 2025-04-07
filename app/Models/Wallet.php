<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasUlids;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'account',
        'type',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transfer::class);
    }
}
