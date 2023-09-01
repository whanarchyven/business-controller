<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $salary->save();
        }
    }
}
