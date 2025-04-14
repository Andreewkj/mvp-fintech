<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
