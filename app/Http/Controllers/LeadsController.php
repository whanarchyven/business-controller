<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CoordinatorController;
use MongoDB\Driver\Session;


class LeadsController extends Controller
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
                'leads' => 0,
                'declined' => 0,
                'successful' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getMonthLeads($year, $month, $isDeclined)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $user = Auth::user();

        $city = City::where(["id" => $user->city])->first();

        if ($user->hasRole('operator')) {
            return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['operator_id', '=', $user->id]])->get();
        } else {
            if ($user->isAdmin) {
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', "=", \Illuminate\Support\Facades\Session::get('city')->name]])->get();
            } else {
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', '=', $city->name]])->get();
            }
        }
    }

    public function getOperatorMonthLeads($year, $month, $type, $operator_id)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

//        dd($startDate, $endDate);

        switch ($type) {
            case 'all':
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['operator_id', '=', $operator_id]])->get();
            case 'successful':
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', '=', 'in-work'], ['operator_id', '=', $operator_id]])->orWhere([['status', '=', 'accepted'], ['operator_id', '=', $operator_id]])->orWhere([['status', '=', 'completed'], ['operator_id', '=', $operator_id]])->whereBetween('created_at', [$startDate, $endDate])->get();
            case 'declined':
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', '=', 'declined'], ['operator_id', '=', $operator_id]])->get();
            default:
                return Lead::whereBetween('created_at', [$startDate, $endDate])->get();
        }
    }


    public function indexOperator(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = $user->city;
        $city = City::where(["id" => $city])->first();


        if ($user->hasRole('operator')) {
            $leads = Lead::where([['status', '!=', 'declined'], ['operator_id', '=', $user->id]])->whereDate('created_at', $date)->get();
        } else if ($user->isAdmin) {
            $leads = Lead::where([['status', '!=', 'declined'], ['city', '=', \Illuminate\Support\Facades\Session::get('city')->name]])->whereDate('created_at', $date)->get();
        } else {
            $leads = Lead::where([['status', '!=', 'declined'], ['city', '=', $city->name]])->whereDate('created_at', $date)->get();
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


        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $monthLeads = $this->getMonthLeads($dateTemp[0], $dateTemp[1], false);

        $totalLeads = 0;

        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['leads'] += 1;
            $totalLeads++;
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


        return view('lead.show', compact('leads', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalLeads', 'nextMonthLink', 'prevMonthLink'));
    }

    public function getDeclined(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = $user->city;
        $city = City::where(["id" => $city])->first();


        if ($user->hasRole('operator')) {
            $leads = Lead::where([['status', '=', 'declined'], ['operator_id', '=', $user->id]])->whereDate('created_at', $date)->get();
        } else if ($user->isAdmin) {
            $leads = Lead::where([['status', '=', 'declined'], ['city', '=', \Illuminate\Support\Facades\Session::get('city')->name]])->whereDate('created_at', $date)->get();
        } else {
            $leads = Lead::where([['status', '=', 'declined'], ['city', '=', $city->name]])->whereDate('created_at', $date)->get();
        }


        $dateTemp = preg_split("/[^1234567890]/", $date);
//            dd($dateTemp);
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

        $monthLeads = $this->getMonthLeads($dateTemp[0], $dateTemp[1], true);

        $totalLeads = 0;

        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['leads'] += 1;
            $totalLeads++;
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

        return view('lead.declined', compact('leads', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalLeads', 'nextMonthLink', 'prevMonthLink'));
    }

    public function create()
    {
        $user = Auth::user();
        $cities = City::all();
        return view('lead.create', compact('cities', 'user'));
    }

    public function getLeadsByOperatorId(User $user, Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
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

        $city = $user->city;

        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $monthLeads = $this->getOperatorMonthLeads($dateTemp[0], $dateTemp[1], 'all', $user->id);
        $successful_leads = $this->getOperatorMonthLeads($dateTemp[0], $dateTemp[1], 'successful', $user->id);
        $declined_leads = $this->getOperatorMonthLeads($dateTemp[0], $dateTemp[1], 'declined', $user->id);

//        dd($successful_leads);

        $totalLeads = 0;
        $totalDeclined = 0;
        $totalSuccessful = 0;

        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['created_at'])[2]);
            $days[$day - 1]['leads'] += 1;
            $totalLeads++;
        }

        foreach ($declined_leads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['created_at'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
        }

        foreach ($successful_leads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['created_at'])[2]);
            $days[$day - 1]['successful'] += 1;
            $totalSuccessful++;
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

        $totalWorkDays = 0;

        foreach ($days as $day) {
            if ($day['leads'] != 0) {
                $totalWorkDays++;
            }
        }

//        dd($successful_leads);


        return view('cards.operator', compact('date', 'dateTitle', 'formattedDate', 'city', 'user', 'days', 'totalLeads', 'nextMonthLink', 'prevMonthLink', 'totalSuccessful', 'totalDeclined', 'totalWorkDays'));
    }


    public function edit(Lead $lead)
    {
        $cities = City::all();
        return view('lead.edit', compact('lead', 'cities'));
    }

    public function store()
    {
        $data = \request()->validate([
            'id' => '',
            'created_at' => '',
            'updated_at' => '',
            'operator_id' => '',
            'city' => '',
            'subcity' => '',
            'address' => '',
            'time_period' => '',
            'client_fullname' => '',
            'phone' => '',
            'comment' => '',
            'job_type' => '',
            'range' => '',
            'measuring' => '',
            'note' => '',
        ]);
        $user = Auth::user()->id;
        $data['operator_id'] = $user;
        $data['status'] = 'not-managed';
        $data['meeting_date'] = Carbon::now()->toDateString();


        if (isset($data['measuring'])) {
            $data['measuring'] == 'on' ? $data['measuring'] = true : $data['measuring'] = false;
        }

        if (isset($data['range'])) {
            $data['range'] == 'on' ? $data['range'] = true : $data['range'] = false;
        }


//        dd($data);

        if (Auth::user()->hasRole('operator')) {
//            dd('aaaa');
            $tempDate = Carbon::now()->toDateString();
            $tempDate = preg_split("/[^1234567890]/", $tempDate);

//            dd(count(Lead::whereDate('created_at', Carbon::today())->where([['operator_id', '=', Auth::user()->id]])->get()));


            if (count(Lead::whereDate('created_at', Carbon::today())->where([['operator_id', '=', Auth::user()->id]])->get()) == 0) {
                app('App\Http\Controllers\SalaryController')->addSalary(Auth::user(), 200);
            }
        }

        $lead = Lead::create($data);
        return redirect()->route('leads.index');
    }

    public function update(Lead $lead)
    {
        $data = \request()->all();
        $user = Auth::user()->id;
        $data['operator_id'] = $user;
        $data['status'] = 'not-managed';


        if (isset($data['measuring'])) {
            $data['measuring'] == 'on' ? $data['measuring'] = true : $data['measuring'] = false;
        }

        if (isset($data['range'])) {
            $data['range'] == 'on' ? $data['range'] = true : $data['range'] = false;
        }
        $lead->update($data);
        if (Auth::user()->hasRole('operator')) {
            return redirect()->route('leads.index');
        } elseif (Auth::user()->hasRole('coordinator')) {
            return redirect()->route('coordinator.managers');
        }
    }


    //MANAGER


    public function getManagerLeads()
    {
        $manager = Auth::user();
        $leads = Lead::where([['manager_id', '=', $manager->id]])->get();

        return view('roles.manager.leads', compact('leads', 'manager'));
    }

    public function changeLeadStatus(Lead $lead)
    {
        $data = \request()->validate([
            'status' => '',
        ]);

        $manager = $lead->getManagerId;
        $operator = $lead->getOperatorId;

        $lead->update([$data['status'] => Carbon::now()->toTimeString()]);
        if ($data['status'] == 'accepted') {
            $lead->update(['status' => 'accepted']);
            $manager->status = 'meeting-accepted';
            $manager->save();
        } else if ($data['status'] == 'entered') {
            $lead->update(['status' => 'in-work']);
            $manager->status = 'on-meeting';
            $manager->save();
            app('\App\Http\Controllers\SalaryController')->addSalary($operator, 150);
            $operator->save();
        } else if ($data['status'] == 'exited') {
            $manager->status = 'free';
            $manager->save();
        }

        return redirect()->back();
    }

    public function closeLeadMeeting(Lead $lead, Request $request)
    {
        $data = \request()->all();

        $lead->update(['check' => $data['check'], 'status' => 'in-work',]);
        return redirect()->back();
    }

    public function getManagerDaysInMonthWithWeekdays($month, $year)
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
                'meetings' => 0,
                'successful' => 0,
                'declined' => 0,
                'workDay' => false,
                'products_selled' => 0,
                'products_issued' => 0,
                'products_confirmed' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getManagerMonthLeads($year, $month, $type, $manager_id)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        switch ($type) {
            case 'all':
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['manager_id', '=', $manager_id]])->get();
            case 'successful':
                return Lead::where([['status', '=', 'completed'], ['manager_id', '=', $manager_id]])->orWhere([['status', '=', 'in-work'], ['manager_id', '=', $manager_id]])->whereBetween('created_at', [$startDate, $endDate])->get();
            case 'declined':
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', '=', 'declined'], ['manager_id', '=', $manager_id]])->get();
            default:
                return Lead::whereBetween('created_at', [$startDate, $endDate])->get();
        }
    }


    public function declineLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $lead->update(['status' => 'declined', 'note' => $data['note']]);
        return redirect(route('manager.leads'));
    }

    public function managerCard(User $manager, Request $request)
    {

        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        if (!$manager->id) {
            $manager = Auth::user();
        }


        [$manager, $manager_statuses] = app('App\Http\Controllers\CoordinatorController')->getManagerCard($manager->id);


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

        $days = $this->getManagerDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $monthLeads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'all', $manager->id);
        $successful_leads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'successful', $manager->id);
        $declined_leads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'declined', $manager->id);

        $totalMeetings = 0;
        $totalDeclined = 0;
        $totalSuccessful = 0;

        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['meetings'] += 1;
            $days[$day - 1]['products_selled'] += $lead['check'];
            $days[$day - 1]['products_issued'] += $lead['issued'];
            $days[$day - 1]['products_confirmed'] += $lead->repair ? $lead->repair->check : 0;
            $totalMeetings++;
        }

        foreach ($declined_leads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
        }

        foreach ($successful_leads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['successful'] += 1;
            $totalSuccessful++;
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

        $totalWorkDays = 0;
        $totalSelled = 0;
        $totalIssued = 0;
        $totalConfirmed = 0;

        foreach ($days as $day) {
            if ($day['products_issued'] != 0) {
                $totalWorkDays++;
            }
            $totalSelled += $day['products_selled'];
            $totalIssued += $day['products_issued'];
            $totalConfirmed += $day['products_confirmed'];
        }

        $leads = Lead::where([['manager_id', '=', $manager->id], ['check', '=', null]])->get();

        $documents = explode('|', $manager->documents);


        if ($manager->hasRole('manager')) {
            return view('cards.manager', compact('date', 'dateTitle', 'formattedDate', 'city', 'manager', 'manager_statuses', 'days', 'totalMeetings', 'nextMonthLink', 'prevMonthLink', 'totalSuccessful', 'totalDeclined', 'totalWorkDays', 'totalSelled', 'leads', 'totalIssued', 'totalConfirmed', 'documents'));
        }


    }

}
