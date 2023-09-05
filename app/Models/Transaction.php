<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'state_id',
        'description',
        'type',
        'responsible',
        'documents',
        'balance_stamp',
        'value'
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'responsible');
    }

    public function state()
    {
        return $this->belongsTo(TransactionState::class, 'state_id');
    }
}
