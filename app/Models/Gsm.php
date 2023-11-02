<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gsm extends Model
{
    use HasFactory;

    protected $fillable=['user_id', 'city_id', 'amount', 'is_payed'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed()->first();
    }
}
