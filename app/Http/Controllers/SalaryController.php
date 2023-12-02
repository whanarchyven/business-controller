<?php

namespace App\Http\Controllers;

use App\Models\BonusManager;
use App\Models\City;
use App\Models\DirectorWorkday;
use App\Models\EmployeerWorkDay;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\Repair;
use App\Models\Salary;
use App\Models\TransactionState;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function addSalary(User $user, $money)
    {
        $date = Carbon::now()->toDateString();
        $yearTemp = preg_split("/[^1234567890]/", $date)[0];
        $monthTemp = preg_split("/[^1234567890]/", $date)[1];
        $salary = Salary::where(["month" => $monthTemp, "year" => $yearTemp, "user_id" => $user->id])->first();
        if ($salary) {
            $salary->salary += $money;
            $salary->save();
        } else {
            $salary = new Salary(["month" => $monthTemp, "year" => $yearTemp, "user_id" => $user->id, "salary" => $money]);
            $salary->salary += $money;
            $salary->save();
        }
        if ($user->hasRole('manager')) {
            $state = TransactionState::getByCode('2.01.2.3.');
        } elseif ($user->hasRole('master')) {
            $state = TransactionState::getByCode('2.01.2.1.');
        } elseif ($user->hasRole('director')) {
            $state = TransactionState::getByCode('2.01.2.2.');
        } elseif ($user->hasRole('operator')) {
            $state = TransactionState::getByCode('2.01.2.10.');
        }
        $desc = 'Выплата аванса ' . $user->name . ' ' . Carbon::today()->toDateString();
        $value = $money;
        $responsible = Auth::user()->id;
        $city_id = $user->city;
        $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city_id, '');
    }

    public function getDaysInMonthWithWeekdays($month, $year)
    {
        $firstDay = strtotime("$year-$month-01");
        $daysInMonth = date('t', $firstDay);

        $result = array();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = strtotime("$year-$month-$day");
            $weekDay = date('N', $currentDate); // 1 (понедельник) до 7 (воскресенье)

            $weekDays = array(
                1 => 'пн',
                2 => 'вт',
                3 => 'ср',
                4 => 'чт',
                5 => 'пт',
                6 => 'сб',
                7 => 'вс'
            );

            $result[] = array(
                'day' => $day,
                'date' => $year . '-' . $month . '-' . ($day < 10 ? '0' . $day : $day),
                'weekDay' => $weekDays[$weekDay],
            );
        }

        return $result;
    }


    public function getDirectorSalary(User $user, $date)
    {
        $city = $user->city();
//        $date = Carbon::today()->toDateString();
        $date = explode('-', $date);

        $startDate = Carbon::createFromDate($date[0], $date[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($date[0], $date[1], 1)->endOfMonth();
        $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', 'completed']])->get();

        $days = $this->getDaysInMonthWithWeekdays($date[1], $date[0]);
        $monthWorkDays = 0;
        foreach ($days as $day) {
            if ($day['weekDay'] != 'вс') {
                $monthWorkDays++;
            }
        }

        $totalConfirmed = 0;
        $confirmed = array();
        foreach ($repairs as $repair) {
            if ($repair->lead->city == $city->name) {
                array_push($confirmed, $repair);
                $totalConfirmed += $repair->check;
            }
        }
        $workDays = DirectorWorkday::where([['director_id', '=', $user->id]])->whereBetween('created_at', [$startDate, $endDate])->get();

        $totalSalary = 0;

        $employers = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($employers as $employer) {
            if (!$employer->hasRole('director') && !$employer->hasRole('operator')) {
                array_push($temp, $employer);
            }
        }
        $employers = $temp;

        if ($totalConfirmed < 1000000) {
            $totalSalary = round(50000 / $monthWorkDays * count($workDays));
        } elseif ($totalConfirmed >= 1000000 && $totalConfirmed < 2000000) {
            $totalSalary = round($totalConfirmed * 0.09);
        } elseif ($totalConfirmed >= 2000000 && $totalConfirmed < 3000000) {
            $totalSalary = round($totalConfirmed * 0.10);
        } elseif ($totalConfirmed >= 3000000) {
            if (count($employers) >= 11) {
                $totalSalary = round($totalConfirmed * 0.11);
            } else {
                $totalSalary = round($totalConfirmed * 0.10);
            }
        }

        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $user->id, "type" => 'minus'])->get();
        $totalDeduction = 0;
        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }

        $totalSalary -= $totalDeduction;

        return round($totalSalary);

    }

    public function getManagerSalary(User $user, $date)
    {

        //Оклад + 10% от ТО + 1% отказ + 1% (конверсия 50%)

        $city = $user->city();
//        $date = Carbon::today()->toDateString();
        $date = explode('-', $date);

        $startDate = Carbon::createFromDate($date[0], $date[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($date[0], $date[1], 1)->endOfMonth();
        $leads = Lead::whereBetween('created_at', [$startDate, $endDate])->where(["status" => 'completed', "manager_id" => $user->id])->get();
        $declinedLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->where(["status" => 'declined', "manager_id" => $user->id])->get();
        $days = $this->getDaysInMonthWithWeekdays($date[1], $date[0]);
        $monthWorkDays = 0;
        foreach ($days as $day) {
            if ($day['weekDay'] != 'вс') {
                $monthWorkDays++;
            }
        }

        $totalConfirmed = 0;
        $totalEnter = 0;
        $totalNullLeads = 0;
        $totalDeclined = 0;
        foreach ($leads as $lead) {
            if ($lead->repair && $lead->repair->status == 'completed') {
                $totalConfirmed += $lead->repair->check;
            }
            if ($lead->entered) {
                $totalEnter++;
            }
        }

        foreach ($declinedLeads as $lead) {
            if ($lead->issued == 0) {
                $totalNullLeads++;
            }
            $totalDeclined++;
        }

        if ($totalEnter != 0) {
            $conversion = $totalEnter / ($totalNullLeads + $totalEnter);
        } else {
            $conversion = 0;
        }

        $totalWorkDays = count(EmployeerWorkDay::where([['user_id', '=', $user->id]])->whereBetween('created_at', [$startDate, $endDate])->get());

        $oklad = 0;
        $okladSallary = 0;

        if ($totalConfirmed < 200000) {
            $oklad = 5000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 200000 && $totalConfirmed < 300000) {
            $oklad = 15000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 300000 && $totalConfirmed < 400000) {
            $oklad = 25000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 400000 && $totalConfirmed < 500000) {
            $oklad = 40000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 500000 && $totalConfirmed < 700000) {
            $oklad = 50000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 700000 && $totalConfirmed < 900000) {
            $oklad = 70000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 900000 && $totalConfirmed < 1000000) {
            $oklad = 80000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 1000000 && $totalConfirmed < 1500000) {
            $oklad = 100000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 1500000 && $totalConfirmed < 2000000) {
            $oklad = 120000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        } elseif ($totalConfirmed >= 2000000) {
            $oklad = 150000;
            $okladSallary = $oklad * $totalWorkDays / $monthWorkDays;

        }

//        dd($conversion);

        $totalProductsPercent = 0.1;
        if ($conversion >= 0.5) {
            $totalProductsPercent += 0.01;
        }
        if ($totalDeclined < 3) {
            $totalProductsPercent += 0.01;

        }
        if ($totalConfirmed >= 400000) {
            $totalProductsPercent += 0.01;
        }

        $totalProductsSalary = $totalConfirmed * $totalProductsPercent;

        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $user->id, "type" => 'minus'])->get();
        $totalDeduction = 0;
        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }


        $totalSalary = $totalProductsSalary + $okladSallary - $totalDeduction;
//        dd($oklad);

        return round($totalSalary);

    }


    public function getMasterSalary(User $user, $date)
    {
        $city = $user->city();
//        $date = Carbon::today()->toDateString();
        $date = explode('-', $date);

        $startDate = Carbon::createFromDate($date[0], $date[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($date[0], $date[1], 1)->endOfMonth();
        $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where(["status" => 'completed', "master_id" => $user->id])->get();

        $totalConfirmed = 0;
        foreach ($repairs as $repair) {
            if ($repair->lead->city == $city->name) {
                $totalConfirmed += $repair->check;
            }
        }

        $totalSalary = $totalConfirmed * 0.1;

        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $user->id, "type" => 'minus'])->get();
        $totalDeduction = 0;
        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }

        $totalSalary -= $totalDeduction;

        return round($totalSalary);
    }

    public function getOperatorSalary(User $user, $date)
    {
        $city = $user->city();
        $deductions = $user->deductions($date);
        $date = explode('-', $date);

        $startDate = Carbon::createFromDate($date[0], $date[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($date[0], $date[1], 1)->endOfMonth();
        $leads = Lead::whereBetween('created_at', [$startDate, $endDate])->where([["operator_id", '=', $user->id], ["entered", '!=', null]])->get();

        $okna=0;
        $other=0;

        foreach ($leads as $lead){
            if($lead->job_type==1){
                $okna++;
            }
            else{
                $other++;
            }
        }

        $leadsSalary=$okna*200 + $other*150;

        $workDays = count(EmployeerWorkDay::where([['user_id', '=', $user->id]])->whereBetween('created_at', [$startDate, $endDate])->get());
        $daysSalary = $workDays * 200;

        $totalSalary = $daysSalary + $leadsSalary - $deductions;

        return round($totalSalary);
    }


}



