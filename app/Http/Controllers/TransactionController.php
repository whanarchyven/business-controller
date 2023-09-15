<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Transaction;
use App\Models\TransactionState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MongoDB\Driver\Session;

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


    public function newTransactionView()
    {
        $states = TransactionState::all();
        return view('roles.director.transaction_create', compact('states'));
    }

    public function storeNewTransaction(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = \Illuminate\Support\Facades\Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }
        $state = TransactionState::where(["code" => $data['state']])->first();

        $documents = array();
        if ($files = $request->file('documents')) {
            $i = 1;
            foreach ($files as $file) {
                $name = Carbon::now()->toDateString() . '- Ручная транзакция- ' . $user->name . $i . '.' . $file->extension();
//                $name = Carbon::now()->toDateString() . '-' . preg_split("/[\s,]+/", $repair->lead['client_fullname'])[0] . '-' . $i . '.' . $file->extension()
                $file->move('documents', $name);
                $documents[] = $name;
                $i++;
            }
        }
        $documents = implode('|', $documents);
        if ($data['receipt']) {
            $receipt = $this->newReceipt($state->id, 'Ручная транзакция: Приход', $data['value'], $user->id, $city->id, $documents);
        } else {
            $expense = $this->newExpense($state->id, 'Ручная транзакция: Приход', $data['value'], $user->id, $city->id, $documents);
        }

        return redirect(route('director.transactions'));
    }

}
