<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LeadsController;
use App\Models\City;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoordinatorController extends Controller
{
    public function getManagers($city_id)
    {
        $temp = User::where(['city' => $city_id])->get();

        $managers = array();

        foreach ($temp as $temp_user) {
            if ($temp_user->hasRole('manager')) {
                array_push($managers, $temp_user);
            }
        }

        return $managers;
    }

    public function getCities()
    {
        return City::all();
    }


    public function getMonthLeads($isDeclined, $city)
    {
        $date = Carbon::now()->toDateString();
        $startDate = Carbon::createFromDate(intval(preg_split("/[^1234567890]/", $date)[0]), intval(preg_split("/[^1234567890]/", $date)[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate(intval(preg_split("/[^1234567890]/", $date)[0]), intval(preg_split("/[^1234567890]/", $date)[1]), 1)->endOfMonth();
        return Lead::whereBetween('meeting_date', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', '=', $city]])->get();

    }

    public function getMonth()
    {
        switch (preg_split("/[^1234567890]/", Carbon::now()->toDateString())[1]) {
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
            default:
                $dateTitle = 'Январь';
        }
        return $dateTitle;
    }

    public function getTodayLeads($isDeclined, $city)
    {
        return Lead::where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', '=', $city], ['meeting_date', '=', Carbon::now()->toDateString()]])->get();
    }

    public function declineLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $lead->update(['status' => 'declined', 'note' => $data['note']]);
        $lead->getManagerId()->status='free';
        return redirect(route('coordinator.managers'));
    }

    public function update(Lead $lead)
    {
        $data = \request()->all();
        $user = Auth::user()->id;
        $data['status'] = 'not-managed';


        if (isset($data['measuring'])) {
            $data['measuring'] == 'on' ? $data['measuring'] = true : $data['measuring'] = false;
        }

        if (isset($data['range'])) {
            $data['range'] == 'on' ? $data['range'] = true : $data['range'] = false;
        }
//        dd($data);
        $lead->update($data);
        return redirect()->route('coordinator.managers');
    }


    public function controlTable(Request $request)
    {

        $data = $request->all();
        $user = Auth::user();
        $city_id = Session::get('city')->id;

        $city = City::where([['id', '=', $city_id]])->first()->name;

//        dd($city_id);

        $leads = $this->getMonthLeads(false, $city);
        $declined = $this->getMonthLeads(true, $city);
        $month = $this->getMonth();

        $products_selled = 0;
        $products_issued = 0;

        $repairs = array();

        foreach ($leads as $lead) {
            $products_selled += $lead->check;
            if ($lead->repair && $lead->repair->status == 'completed') {
                $products_issued += $lead->repair->check;
                array_push($repairs, $lead->repair);
            }
        }

        $todayLeads = $this->getTodayLeads(false, $city);
        $todayDeclined = $this->getTodayLeads(true, $city);


        $todayProductsSelled = 0;
        $todayProductsIssued = 0;
        $todayTotalLeads = 0;

        foreach ($todayLeads as $lead) {
            $todayProductsSelled += $lead->check;
            $todayTotalLeads++;
            if ($lead->repair && $lead->repair->status == 'completed') {
                $todayProductsIssued += $lead->repair->check;
            }
        }

        $cities = $this->getCities();
        $managers = $this->getManagers($city_id);

        $date = Carbon::now()->toDateString();
        $yearTemp = preg_split("/[^1234567890]/", $date)[0];
        $monthTemp = preg_split("/[^1234567890]/", $date)[1];

        $plan = Plan::where([['year', '=', $yearTemp], ['month', '=', $monthTemp], ['city_id', '=', $city_id]])->first();


        return view('roles.coordinator.control', compact('cities', 'managers', 'city_id', 'leads', 'declined', 'month', 'products_selled', 'todayLeads', 'todayProductsSelled', 'todayDeclined', 'plan', 'city_id', 'user', 'products_issued', 'todayProductsIssued', 'todayTotalLeads'));
    }

    public function manageLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();

        $lead->update(["manager_id" => $manager->id, "status" => 'managed']);
        $manager->status = 'meeting-managed';
        $manager->save();

        return redirect(route('coordinator.managers'));

    }

    public function changeManager(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();
        $lead->update(["manager_id" => null]);
        $manager->status = 'free';
        $manager->save();
        $lead->save();
        return redirect()->back();
    }

    public function getStatuses()
    {
        return [
            "free" => "Свободен",
            "dinner" => "Обед",
            "weekend" => "Выходной",
            "meeting-managed" => "Встреча назначена",
            "meeting-accepted" => "Встреча принята",
            "on-meeting" => "На встрече",
            "delaying" => "'Задреживается'",
        ];
    }

    public function getManagerCard($manager_id)
    {
        $manager = User::where([['id', '=', $manager_id]])->first();

        $manager_statuses = $this->getStatuses();

        return [$manager, $manager_statuses];
    }

    public function changeCity(City $city, Request $request)
    {
        $request->session()->put('city', $city);
        return redirect()->back();
    }
}
