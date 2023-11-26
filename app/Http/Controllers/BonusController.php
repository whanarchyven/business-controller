<?php

namespace App\Http\Controllers;

use App\Models\BonusManager;
use App\Models\City;
use App\Models\Lead;
use App\Models\TransactionState;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = 'Январь ';
                break;
            case '02':
                $dateTitle = 'Февраль ';
                break;
            case '03':
                $dateTitle = 'Март ';
                break;
            case '04':
                $dateTitle = 'Апрель ';
                break;
            case '05':
                $dateTitle = 'Май ';
                break;
            case '06':
                $dateTitle = 'Июнь ';
                break;
            case '07':
                $dateTitle = 'Июль ';
                break;
            case '08':
                $dateTitle = 'Август ';
                break;
            case '09':
                $dateTitle = 'Сентябрь ';
                break;
            case '10':
                $dateTitle = 'Октябрь ';
                break;
            case '11':
                $dateTitle = 'Ноябрь ';
                break;
            case '12':
                $dateTitle = 'Декабрь ';
                break;
        }
        $dateTitle = $dateTemp[2].' '. $dateTitle . $dateTemp[0];

        $formattedDate = $dateTemp[2] . '.' . $dateTemp[1] . '.' . $dateTemp[0];


        $lexems = preg_split("/[^1234567890]/", $date);

//        if (intval($lexems[1]) + 1 < 10) {
//            $nextMonthLink = $lexems[0] . ('-0' . (intval($lexems[1]) + 1)) . '-01';
//        } else {
//            if (intval($lexems[1]) + 1 > 12) {
//                $nextMonthLink = intval($lexems[0]) + 1 . '-01' . '-01';
//            } else {
//                $nextMonthLink = $lexems[0] . ('-' . (intval($lexems[1]) + 1)) . '-01';
//            }
//        }
//
//        if (intval($lexems[1]) - 1 >= 10) {
//            $prevMonthLink = $lexems[0] . ('-' . (intval($lexems[1]) - 1)) . '-01';
//        } else {
//            if (intval(intval($lexems[1]) - 1 <= 0)) {
//                $prevMonthLink = intval($lexems[0]) - 1 . '-12' . '-01';
//            } else {
//                $prevMonthLink = $lexems[0] . ('-0' . (intval($lexems[1]) - 1)) . '-01';
//            }
//        }

        $prevMonthLink=Carbon::createFromDate($date)->subDays(1)->toDateString();
        $nextMonthLink=Carbon::createFromDate($date)->addDays(1)->toDateString();

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();
//        dd(Carbon::today()->toDateString());

        $bonuses = BonusManager::where(["city_id" => $city->id, "type" => "plus"])->whereDate("created_at",Carbon::createFromDate($date)->toDateString())->get();


        $bonuses15k = array();
        $bonuses50k = array();
        $bonuses100k = array();
        $otherBonuses = array();


        foreach ($bonuses as $bonus) {
            $temp = explode(' ', $bonus->reason);
            if (count($temp) >= 3) {
                $tempStr = $temp[0] . ' ' . $temp[1] . ' ' . $temp[2];
                if ($tempStr == 'Бонус за 15000') {
                    array_push($bonuses15k, $bonus);
                } elseif ($tempStr == 'Бонус за 50000') {
                    array_push($bonuses50k, $bonus);
                } elseif ($tempStr == 'Бонус за 100000') {
                    array_push($bonuses100k, $bonus);
                } else {
                    array_push($otherBonuses, $bonus);
                }
            } else {
                array_push($otherBonuses, $bonus);
            }
        }

//        dd($bonuses15k, $bonuses50k, $bonuses100k,);

        return view('roles.director.bonus', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'bonuses50k', 'bonuses15k', 'bonuses100k', 'otherBonuses'));

    }


    public function deduction(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = 'Январь ';
                break;
            case '02':
                $dateTitle = 'Февраль ';
                break;
            case '03':
                $dateTitle = 'Март ';
                break;
            case '04':
                $dateTitle = 'Апрель ';
                break;
            case '05':
                $dateTitle = 'Май ';
                break;
            case '06':
                $dateTitle = 'Июнь ';
                break;
            case '07':
                $dateTitle = 'Июль ';
                break;
            case '08':
                $dateTitle = 'Август ';
                break;
            case '09':
                $dateTitle = 'Сентябрь ';
                break;
            case '10':
                $dateTitle = 'Октябрь ';
                break;
            case '11':
                $dateTitle = 'Ноябрь ';
                break;
            case '12':
                $dateTitle = 'Декабрь ';
                break;
        }
        $dateTitle = $dateTitle . $dateTemp[0];

        $formattedDate = $dateTemp[2] . '.' . $dateTemp[1] . '.' . $dateTemp[0];


        $lexems = preg_split("/[^1234567890]/", $date);

        if (intval($lexems[1]) + 1 < 10) {
            $nextMonthLink = $lexems[0] . ('-0' . (intval($lexems[1]) + 1)) . '-01';
        } else {
            if (intval($lexems[1]) + 1 > 12) {
                $nextMonthLink = intval($lexems[0]) + 1 . '-01' . '-01';
            } else {
                $nextMonthLink = $lexems[0] . ('-' . (intval($lexems[1]) + 1)) . '-01';
            }
        }

        if (intval($lexems[1]) - 1 >= 10) {
            $prevMonthLink = $lexems[0] . ('-' . (intval($lexems[1]) - 1)) . '-01';
        } else {
            if (intval(intval($lexems[1]) - 1 <= 0)) {
                $prevMonthLink = intval($lexems[0]) - 1 . '-12' . '-01';
            } else {
                $prevMonthLink = $lexems[0] . ('-0' . (intval($lexems[1]) - 1)) . '-01';
            }
        }

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["city_id" => $city->id, "type" => "minus"])->get();

        return view('roles.director.deduction', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'deductions'));

    }


    public function payBonus(BonusManager $bonus)
    {
        $user = Auth::user();

        $employer = $bonus->user;

        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }

        if ($employer->hasRole('manager')) {
            $state = TransactionState::getByCode('2.01.1.1.');
        } elseif ($employer->hasRole('director')) {
            $state = TransactionState::getByCode('2.01.1.5.');
        }

        $desc = 'Выплата бонуса ' . $bonus->user->name . ' - ' . $bonus->reason;
        $value = $bonus->amount;
        $responsible = $user->id;
        $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city->id, '');
        $bonus->isPayed = true;
        $bonus->save();

        return redirect()->back();
    }


    public function deleteDeduction(BonusManager $bonus)
    {

        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }
        $state = TransactionState::getByCode('1.1.1.');
        $desc = 'Отмена штрафа менеджеру ' . $bonus->user->name . ' - ' . $bonus->reason;
        $value = $bonus->amount;
        $responsible = $user->id;

        if ($bonus->isPayed) {
            $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city->id, '');
        }


        $bonus->delete();
        return redirect()->back();
    }


    public function deleteBonus(BonusManager $bonus)
    {

        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(["id" => $user->city])->first();
        }
        $state = TransactionState::getByCode('2.01.2.6.1.');
        $desc = 'Отмена бонуса менеджеру ' . $bonus->user->name . ' - ' . $bonus->reason;
        $value = $bonus->amount;
        $responsible = $user->id;
        if ($bonus->isPayed) {
            $transaction = app(\App\Http\Controllers\TransactionController::class)->newReceipt($state->id, $desc, $value, $responsible, $city->id, '');
        }


        $bonus->delete();
        return redirect()->back();
    }


    public function createBonus(User $user, Request $request)
    {
        $data = $request->all();
        $bonus = new BonusManager(["user_id" => $user->id, "type" => $data['type'], "amount" => $data['amount'], "reason" => $data['reason'], "city_id" => $user->city]);
        $bonus->save();
        return redirect()->back();
    }

}
