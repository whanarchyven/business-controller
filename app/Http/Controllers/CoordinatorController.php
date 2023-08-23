<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LeadsController;
use App\Models\City;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoordinatorController extends Controller
{
    public function getManagers($city_id)
    {
        $coordinator = Auth::user();
        $temp = $coordinator->getManagersByCoordinator();

        $managers = array();

        foreach ($temp as $record) {
            $temp_user = User::where([['id', '=', $record->manager_id], ['city', '=', $city_id]])->get();
            if (count($temp_user) != 0) {
                array_push($managers, ...$temp_user);
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


        if ($data && $data['city']) {
            $city_id = $data['city'];
        } else {
            $city_id = Auth::user()->city;
        }

        $city = City::where([['id', '=', $city_id]])->first()->name;

        $leads = $this->getMonthLeads(false, $city);
        $declined = $this->getMonthLeads(true, $city);
        $month = $this->getMonth();

        $products_selled = 0;

        foreach ($leads as $lead) {
            $products_selled += $lead->check;
        }

        $todayLeads = $this->getTodayLeads(false, $city);
        $todayDeclined = $this->getTodayLeads(true, $city);

        $todayProductsSelled = 0;

        foreach ($todayLeads as $lead) {
            $todayProductsSelled += $lead->check;
        }

        $cities = $this->getCities();
        $managers = $this->getManagers($city_id);

        return view('roles.coordinator.control', compact('cities', 'managers', 'city_id', 'leads', 'declined', 'month', 'products_selled', 'todayLeads', 'todayProductsSelled', 'todayDeclined'));
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
}
