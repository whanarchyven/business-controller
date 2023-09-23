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
        return $this->belongsTo(User::class, 'master_id')->withTrashed();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'repair_id');
    }

    public function materials()
    {
        return $this->hasManyThrough(NomenclatureExpense::class, Expense::class, 'repair_id', 'expense_id');
    }

    public function materialPrice()
    {
        $materials = $this->materials;
        $summ = 0;
        foreach ($materials as $material) {
            $summ += $material->quantity * $material->nomenclature->price;
        }
        return $summ;
    }

}
