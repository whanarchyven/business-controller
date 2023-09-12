<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function newReceipt($state_id, $description, $value, $responsible, $city_id, $documents)
    {
        $city = City::where(['id' => $city_id])->first();
        $budget = $city->budget();
//        dd($budget);
        $budget->money += $value;
        $budget->save();

        $transaction = new Transaction(["budget_id" => $budget->id, "state_id" => $state_id, "description" => $description, "type" => 'receipt', "responsible" => $responsible, "documents" => $documents, "balance_stamp" => $budget->money, "value" => $value]);
        $transaction->save();

        return $transaction;
    }


    public function newExpense($state_id, $description, $value, $responsible, $city_id, $documents)
    {
        $city = City::where(['id' => $city_id])->first();
        $budget = $city->budget();
//        dd($budget);
        $budget->money -= $value;
        $budget->save();

        $transaction = new Transaction(["budget_id" => $budget->id, "state_id" => $state_id, "description" => $description, "type" => 'expense', "responsible" => $responsible, "documents" => $documents, "balance_stamp" => $budget->money, "value" => $value]);
        $transaction->save();

        return $transaction;
    }

}
