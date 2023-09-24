<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * @var mixed|string
     */
    protected $guarded = [];

    public function budget()
    {
        return $this->hasOne(Budget::class, 'city_id')->first();
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->get();
    }

    public function transactionsPaginate()
    {
        return $this->hasManyThrough(Transaction::class, Budget::class, 'city_id', 'budget_id')->orderBy('created_at','desc');
    }
}
