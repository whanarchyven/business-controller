<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function jobType()
    {
        return $this->belongsTo(ServiceType::class, 'job_type');
    }

    public function getManagerId()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function getOperatorId()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
