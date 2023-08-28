<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    public function data()
    {
        return $this->hasMany(NomenclatureReceipt::class, 'receipt_id');
    }
}
