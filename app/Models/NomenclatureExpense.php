<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomenclatureExpense extends Model
{
    use HasFactory;

    public function nomenclature()
    {
        return $this->belongsTo(Nomenclature::class, 'nomenclature_id');
    }
}
