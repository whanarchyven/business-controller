<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    protected $fillable = ["lead_id",
        "master_id",
        "check",
        "repair_date",
        "contract_number",
        "works",
        "documents",
        "status",];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
    }

}
