<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Model<WalletModel>
 */
class WalletModel extends Model
{
    use HasUlids;

    /**
     * @use HasFactory<WalletModel>
     */
    use HasFactory;

    protected $table = 'wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'type',
    ];
}
