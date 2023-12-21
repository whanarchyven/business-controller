<?php

namespace App\Http\Controllers;

use App\Models\BonusManager;
use App\Models\City;
use App\Models\EmployeerWorkDay;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CoordinatorController;
use MongoDB\Driver\Session;
use GuzzleHttp;

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
                'date' => $year . '-' . $month . '-' . ($day < 10 ? '0' . $day : $day),
                'weekDay' => $weekDays[$weekDay],
                'workDay' => 0,
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
            if ($user->isAdmin||$user->hasRole('coordinator')) {
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
                return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['entered', '!=', null], ['operator_id', '=', $operator_id]])->get();
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
        } else if ($user->isAdmin||$user->hasRole('coordinator')) {
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

        $totalCheck=0;
        $totalAvance=0;

        foreach ($leads as $lead){
            if($lead->check){
                $totalCheck+=$lead->check;
            }
            if($lead->avance){
                $totalAvance+=$lead->avance;
            }
        }

        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['created_at'])[2]);
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


        return view('lead.show', compact('leads', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalLeads', 'nextMonthLink', 'prevMonthLink','totalCheck','totalAvance'));
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
        } else if ($user->isAdmin||$user->hasRole('coordinator')) {
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
            $day = intval(preg_split("/[^1234567890]/", $lead['created_at'])[2]);
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
//        dd($successful_leads);

        $totalLeads = 0;
        $totalDeclined = 0;
        $totalSuccessful = 0;

        $okna=0;
        $other=0;


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
            if($lead->job_type==1){
                $okna++;
            }
            else{
                $other++;
            }
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

        $monthWorkDays = app(\App\Http\Controllers\DirectorController::class)->getDirectorWorkDays($dateTemp[0], $dateTemp[1], $user->id);

        foreach ($monthWorkDays as $workDay) {
            $day = intval(preg_split("/[^1234567890]/", $workDay['created_at'])[2]);
            $days[$day - 1]['workDay'] = $workDay->id;
        }
        $weekends = 0;
        $totalWorkDays = 0;
        foreach ($days as $day) {
            if ($day['workDay'] != 0) {
                $totalWorkDays++;
            }
            if ($day['weekDay'] == 'вс') {
                $weekends++;
            }
        }

        $startDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();
        $bonuses = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $user->id, "type" => "plus"])->get();
        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $user->id, "type" => "minus"])->get();
        $documents = explode('|', $user->documents);

        return view('cards.operator', compact('date', 'dateTitle', 'formattedDate', 'city', 'user', 'days', 'totalLeads', 'nextMonthLink', 'prevMonthLink', 'totalSuccessful', 'totalDeclined', 'totalWorkDays', 'weekends', 'bonuses', 'deductions', 'documents','okna','other'));
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

        $tempUser = Auth::user();


//        if ($tempUser->hasRole('operator') && !EmployeerWorkDay::whereDate('created_at', Carbon::today()->toDateString())->where(["user_id" => $tempUser->id])->first()) {
////            dd('suka');
//            $successful_leads = Lead::whereDate('created_at',Carbon::today()->toDateString())->where([['status', '=', 'in-work'], ['operator_id', '=', $tempUser->id]])->orWhere([['status', '=', 'accepted'], ['operator_id', '=', $tempUser->id]])->orWhere([['status', '=', 'completed'], ['operator_id', '=', $tempUser->id]])->whereDate('created_at',Carbon::today()->toDateString())->get();
////            dd(count($successful_leads));
//            if(count($successful_leads)>=1){
//                $workDay = new EmployeerWorkDay(["user_id" => $tempUser->id]);
//                $workDay->save();
//            }
//        }

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

        if (array_key_exists('check',$data)){
            $data['status']='in-work';
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
        $leads = Lead::where([['manager_id', '=', $manager->id],['issued','=',null]])->get();

        return view('roles.manager.leads', compact('leads', 'manager'));
    }

    public function getLinksToStatuses()
    {
        return $links = ["meeting-accepted" => 'https://i.ibb.co/pzq1fBs/accepted.png', "free" => 'https://i.ibb.co/CWHv2SM/free.png', "weekend" => 'https://i.ibb.co/HYqZfnV/weekend.png', "meeting-managed" => "https://i.ibb.co/8zsXb74/managed.png", "dinner" => "https://i.ibb.co/0mNJQLz/dinner.png", "on-meeting" => "https://i.ibb.co/chyhmjN/on-meeting.png", "delaying" => 'https://i.ibb.co/xfgpKH5/delaying.png'];
    }

    public function changeBotPhoto(User $user,$status){
        if ($user->chat_bot_id) {
            $client = new GuzzleHttp\Client();
            $response = $client->request('POST', 'https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/setChatPhoto?chat_id=' . $user->chat_bot_id, [
                'multipart' => [
                    [
                        'name' => 'photo',
                        'contents' => fopen($this->getLinksToStatuses()[$status], 'r')
                    ],
                ]
            ]);
        }
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
            $this->changeBotPhoto($manager,'meeting-accepted');
            $manager->save();
        } else if ($data['status'] == 'entered') {
            $lead->update(['status' => 'in-work']);
            if (!EmployeerWorkDay::whereDate('created_at', Carbon::today()->toDateString())->where(["user_id" => $operator->id])->first()) {
                $workDay = new EmployeerWorkDay(["user_id" => $operator->id]);
                $workDay->save();
            }

            $manager->status = 'on-meeting';
            $this->changeBotPhoto($manager,'on-meeting');
            $manager->save();
            $operator->save();
        } else if ($data['status'] == 'exited') {
            $manager->status = 'free';
            $this->changeBotPhoto($manager,'free');
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
                'date' => $year . '-' . $month . '-' . ($day < 10 ? '0' . $day : $day),
                'weekDay' => $weekDays[$weekDay],
                'meetings' => 0,
                'successful' => 0,
                'declined' => 0,
                'workDay' => 0,
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
                return Lead::where([['issued', '>', 0], ['manager_id', '=', $manager_id]])->whereBetween('created_at', [$startDate, $endDate])->get();
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

        $city = Auth::user()->city;

        $days = $this->getManagerDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $monthLeads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'all', $manager->id);

//        dd($monthLeads);

//        dd($monthLeads);

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $successful_leads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'successful', $manager->id);

//        dd($successful_leads);

        $completedLeads=Lead::whereBetween('created_at', [$startDate, $endDate])->where(["status" => 'completed', "manager_id" => $manager->id])->get();



        $declined_leads = $this->getManagerMonthLeads($dateTemp[0], $dateTemp[1], 'declined', $manager->id);

//        dd($declined_leads);

        $leads_temp=[];



//        dd($monthLeads);


        $totalMeetings = 0;
        $totalDeclined = 0;
        $totalSuccessful = 0;
        $totalNull = 0;
        $totalEnter = 0;



        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['meetings'] += 1;
            $days[$day - 1]['products_selled'] += $lead['check'];
            $days[$day - 1]['products_issued'] += $lead['issued'];
            $days[$day - 1]['products_confirmed'] += $lead->repair&&$lead->repair->status=='completed' ? $lead->repair->check : 0;
            $totalMeetings++;
            if ($lead->entered) {
                $totalEnter++;
            }
            if($lead->check!=null){
                $days[$day - 1]['successful'] += 1;
                $totalSuccessful++;
            }
        }

        foreach ($declined_leads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['meeting_date'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
            if ($lead->issued == 0) {
                $totalNull++;
            }
        }

//        dd($totalDeclined);

//        dd($totalEnter);

//        foreach ($monthLeads as $lead){
//            if($lead->repair){
//                if($lead->marge()>=35&&$lead->repair->status!='declined'){
//                    array_push($leads_temp,$lead);
//                }
//            }
//            else{
//                array_push($leads_temp,$lead);
//            }
//        }
        $monthLeads=$leads_temp;

//        dd($totalEnter);



        $totalWorkDays = 0;
        $totalSelled = 0;
        $totalIssued = 0;
        $totalConfirmed = 0;


        $documents = explode('|', $manager->documents);

        $oklad = 0;
        $okladSallary = 0;
        $weekends = 0;

        $monthWorkDays = app(\App\Http\Controllers\DirectorController::class)->getDirectorWorkDays($dateTemp[0], $dateTemp[1], $manager->id);

        foreach ($monthWorkDays as $workDay) {
            $day = intval(preg_split("/[^1234567890]/", $workDay['created_at'])[2]);
            $days[$day - 1]['workDay'] = $workDay->id;
        }

        foreach ($days as $day) {
            if ($day['workDay'] != 0) {
                $totalWorkDays++;
            }
            if ($day['weekDay'] == 'вс') {
                $weekends++;
            }

            $totalSelled += $day['products_selled'];
            $totalIssued += $day['products_issued'];
            $totalConfirmed += $day['products_confirmed'];
        }

        if ($totalConfirmed < 200000) {
            $oklad = 5000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 200000 && $totalConfirmed < 300000) {
            $oklad = 15000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 300000 && $totalConfirmed < 400000) {
            $oklad = 25000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 400000 && $totalConfirmed < 500000) {
            $oklad = 40000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 500000 && $totalConfirmed < 700000) {
            $oklad = 50000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 700000 && $totalConfirmed < 900000) {
            $oklad = 70000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 900000 && $totalConfirmed < 1000000) {
            $oklad = 80000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 1000000 && $totalConfirmed < 1500000) {
            $oklad = 100000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 1500000 && $totalConfirmed < 2000000) {
            $oklad = 120000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        } elseif ($totalConfirmed >= 2000000) {
            $oklad = 150000;
            $okladSallary = $oklad * $totalWorkDays / (count($days) - $weekends);

        }

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $bonuses = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $manager->id, "type" => "plus"])->get();
        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $manager->id, "type" => "minus"])->get();

        $totalDeduction = 0;

        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }

        $totalBonus = 0;

        foreach ($bonuses as $bonus) {
            if ($bonus->isPayed) {
                $totalBonus += $bonus->amount;
            }
        }

        $totalProductsPercent = 0.1;
        if ($totalEnter != 0) {
            $conversion = $totalEnter / ($totalNull + $totalEnter);
        } else {
            $conversion = 0;
        }
//        dd($conversion);
//        dd($totalConfirmed);

//        dd($conversion,$totalEnter ,$totalNull + $totalEnter );

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

        $lowMargeChecksDeduction=0;

        $lowMargeChecks=Lead::whereBetween('created_at', [$startDate, $endDate])->where([["manager_id","=",$manager->id],['salary_debuff','!=',false]])->get();

        foreach ($lowMargeChecks as $check){
            $lowMargeChecksDeduction+=$check->repair->check;
        }

        $lowMargeChecksDeduction=$lowMargeChecksDeduction*$totalProductsPercent;

        $totalSalary = $totalProductsSalary + $okladSallary - $totalDeduction-$lowMargeChecksDeduction;
//        dd($okladSallary);

        if ($manager->hasRole('manager')) {
            return view('cards.manager', compact('date', 'dateTitle', 'formattedDate', 'city', 'manager', 'manager_statuses', 'days', 'totalMeetings', 'nextMonthLink', 'prevMonthLink', 'totalSuccessful', 'totalDeclined', 'totalWorkDays', 'totalSelled', 'totalIssued', 'totalConfirmed', 'documents', 'oklad', 'okladSallary', 'weekends', 'bonuses', 'deductions', 'totalDeduction', 'totalBonus', 'totalProductsPercent', 'totalSalary','lowMargeChecksDeduction','lowMargeChecks'));
        }


    }

    public function managerOperative(User $manager, Request $request)
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


        $leads = Lead::where([['manager_id', '=', $manager->id], ['issued', '=', null]])->get();


        if ($manager->hasRole('manager')) {
            return view('roles.manager.operative', compact('date', 'manager', 'manager_statuses', 'leads'));
        }


    }

    public function deleteLead(Lead $lead){
        $lead->repair->delete();
        $lead->delete();
        return redirect()->back();
    }


}
