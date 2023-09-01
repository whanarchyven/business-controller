<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Lead;
use App\Models\Repair;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairsController extends Controller
{
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
                'weekDay' => $weekDays[$weekDay],
                'repairs' => 0,
                'declined' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getMonthRepairs($year, $month, $isDeclined)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $user = Auth::user();
        $city = City::where(["id" => $user->city])->first();

        if ($user->isAdmin) {
            return Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined']])->get();
        } else {
            $temp = array();
            $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined']])->get();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            return $temp;
        }
    }


    public function index(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = City::where(["id" => $user->city])->first();
        if ($user->isAdmin) {
            $repairs = Repair::where([['repair_date', '=', $date], ['status', '!=', 'declined']])->get();
        } else {
            $temp = array();
            $repairs = Repair::where([['repair_date', '=', $date], ['status', '!=', 'declined']])->get();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
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

        $city = Auth::user()->city;

        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $monthRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], false);

        $totalRepairs = 0;

        foreach ($monthRepairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['repairs'] += 1;
            $totalRepairs++;
        }

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

        return view('repair.show', compact('repairs', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalRepairs', 'nextMonthLink', 'prevMonthLink'));
    }

    public function update(Repair $repair, Request $request)
    {
        $data = $request->validate([
            "lead_id" => "",
            "master_id" => "",
            "check" => "",
            "repair_date" => "",
            "contract_number" => "",
            "works" => "",
            "documents" => "",
            "status" => "",
        ]);


        if ($data['status'] == 'completed' && ($repair->status == 'declined' || $repair->status == 'in-work')) {
            $repair->check = $repair->lead->issued;
            $repair->save();
            $repair->lead->getManagerId->salary($repair->check * 0.2);
            if ($repair->master) {
                $repair->master->salary($repair->check * 0.1);
            };
        }

        if (($data['status'] == 'declined' || $data['status'] == 'in-work') && $repair->status == 'completed') {
            $repair->check = 0;
            $repair->save();
            $repair->lead->getManagerId->salary($repair->lead->issued * -0.2);
            if ($repair->master) {
                $repair->master->salary($repair->lead->issued * -0.1);
            };
        }

        $repair->update($data);

        return redirect()->back();
    }

    public function edit(Repair $repair)
    {
        $cities = City::all();
        $users = User::where(["city" => Auth::user()->city])->get();

        $managers = array();
        $masters = array();

        foreach ($users as $user) {
            if ($user->hasRole('manager')) {
                array_push($managers, $user);
            }
            if ($user->hasRole('master')) {
                array_push($masters, $user);
            }
        }


        $documents1 = explode('|', $repair->documents);
        $documents2 = explode('|', $repair->lead->documents);
        $documents = array_merge($documents1, $documents2);

        return view('repair.edit', compact('repair', 'cities', 'masters', 'managers', 'documents'));
    }

    public function editRepairViaLead(Repair $repair, Request $request)
    {
        $data = $request->all();

        $documents = array();
        if ($files = $request->file('documents')) {
            $i = 1;
            foreach ($files as $file) {
                $name = Carbon::now()->toDateString() . '-' . $repair->lead->client_fullname . '-' . $repair->lead->city . '-' . $i . '_' . rand(0, 16000) . '.' . $file->extension();
//                $name = Carbon::now()->toDateString() . '-' . preg_split("/[\s,]+/", $repair->lead['client_fullname'])[0] . '-' . $i . '.' . $file->extension()
                $file->move('documents', $name);
                $documents[] = $name;
                $i++;
            }
        }

        $lead = $repair->lead;
        $city = City::where(["name" => $data['city']])->first();

        $lead->update(["meeting_date" => $data['meeting_date'],
            "city" => $city->name,
            "subcity" => array_key_exists('subcity', $data) ? $data['subcity'] : $lead->subcity,
            "address" => $data['address'],
            "phone" => $data['phone'],
            "job_type" => $data['job_type'],
            "issued" => $data["issued"],
            "avance" => $data['avance'],
            "manager_id" => $data['manager_id'],
            "note" => $data['note']
        ]);
        $lead->save();


        $repair->update([
            "contract_number" => $data['contract_number'],
            "works" => $data['works'],
            "master_id" => $data['master_id'],
            "documents" => $repair->documents . '|' . implode('|', $documents),
            "repair_date" => $data['repair_date'],
            "check" => $data['issued']

        ]);
        $repair->save();

        return (redirect(route('repairs.index')));
    }


}
