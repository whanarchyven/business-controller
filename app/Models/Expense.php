<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public function getMaterials()
    {
        return $this->belongsTo(NomenclatureExpense::class, 'expense_id');
    }
}
