<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusManager extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "type", "amount", "reason", "city_id"];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
