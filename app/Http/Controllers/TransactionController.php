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
//        dd($data);
        $user = Auth::user();
        if(array_key_exists('ismainoffice',$data)){
            $city=City::where(["id"=>999])->first();
        }
        else{
            if ($user->isAdmin) {
                $city = \Illuminate\Support\Facades\Session::get('city');
            } else {
                $city = City::where(["id" => $user->city])->first();
            }
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
        if ($data['type']=='receipt') {
            $receipt = $this->newReceipt($state->id, 'Ручная транзакция: Приход', $data['value'], $user->id, $city->id, $documents);
        } else {
            if ($state->code=='3.0.'){
                $expense = $this->newExpense($state->id, 'Перевод в главный офис', $data['value'], $user->id, $city->id, $documents);
                if($city->name=='Нижний Новгород'){
                    $newState=TransactionState::where(["code"=>'3.01.27.'])->first();
                }
                else if($city->name=='Симферополь'){
                    $newState=TransactionState::where(["code"=>'3.01.28.'])->first();
                }
                $perevod =$this->newReceipt($newState->id, 'Приход от филиала', $data['value'], $user->id, 999, $documents);
            }
            else if($state->code=='3.02.27.'){
                $expense = $this->newExpense($state->id, 'Перевод в филиал', $data['value'], $user->id, 999, $documents);
                $newCity=City::where(["name"=>"Нижний Новгород"])->first();
                $newState=TransactionState::where(["code"=>'3.01.'])->first();
                $perevod =$this->newReceipt($newState->id, 'Приход от главного офиса', $data['value'], $user->id, $newCity->id, $documents);
            }
            else if($state->code=='3.02.28.'){
                $expense = $this->newExpense($state->id, 'Перевод в филиал', $data['value'], $user->id, 999, $documents);
                $newCity=City::where(["name"=>"Симферополь"])->first();
                $newState=TransactionState::where(["code"=>'3.01.'])->first();
                $perevod =$this->newReceipt($newState->id, 'Приход от главного офиса', $data['value'], $user->id, $newCity->id, $documents);
            }
            else{
                $expense = $this->newExpense($state->id, 'Ручная транзакция: Расход', $data['value'], $user->id, $city->id, $documents);
            }
        }

        if(array_key_exists('ismainoffice',$data)){
            return redirect(route('director.transactions.mainoffice'));
        }
        else{
            return redirect(route('director.transactions'));
        }
    }

}
