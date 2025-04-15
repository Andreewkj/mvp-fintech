<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TransferModel extends Model
{
    use HasUlids;

    protected $table = 'transfers';

    protected $fillable = [
        'value',
        'payer_wallet_id',
        'payee_wallet_id',
        'status',
        'authorized_at',
        'denied_at',
    ];
}
