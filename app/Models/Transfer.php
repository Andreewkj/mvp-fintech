<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasUlids;

    protected $fillable = [
        'value',
        'payer_wallet_id',
        'payee_wallet_id',
        'reversed_at'
    ];
}
