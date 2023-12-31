<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionState extends Model
{
    use HasFactory;

    static function getByCode($code)
    {
        return TransactionState::where(["code" => $code])->first();
    }
}
