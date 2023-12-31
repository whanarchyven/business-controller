<?php

namespace App\Http\Controllers;

use App\Models\BonusManager;
use App\Models\City;
use App\Models\Lead;
use App\Models\Repair;
use App\Models\TransactionState;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
                'completed' => 0,
                'refund' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getMonthRepairs($year, $month, $status)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $user = Auth::user();
        $city = City::where(["id" => $user->city])->first();

        if ($user->isAdmin) {
            $temp = array();
            switch ($status) {
                case 'all':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->get();
                    break;
                case 'declined':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', $status]])->get();
                    break;
                case 'completed':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', $status]])->get();
                    break;
                case 'refund':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', $status]])->get();
                    break;
                default:
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->get();
                    break;
            }
            foreach ($repairs as $repair) {
                if ($repair->lead->city == Session::get('city')->name) {
                    array_push($temp, $repair);
                }
            }
            return $temp;
        } else {
            $temp = array();
            switch ($status) {
                case 'all':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->get();
                    break;
                case 'declined':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', 'declined']])->get();
                    break;
                case 'completed':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', 'completed']])->get();
                    break;
                case 'refund':
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', $status]])->get();
                    break;
                default:
                    $repairs = Repair::whereBetween('repair_date', [$startDate, $endDate])->get();
                    break;
            }
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
            $temp = array();

            $repairs = Repair::where([['repair_date', '=', $date]])->get();
            foreach ($repairs as $repair) {

                if ($repair->lead->city == (Session::get('city')->name)) {

                    array_push($temp, $repair);
                }
            }

            $repairs = $temp;
        } else {
            $temp = array();
            $repairs = Repair::where([['repair_date', '=', $date]])->get();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
        }

//        foreach ($repairs as $repair){
//            dd($repair->getResult('sdfs'));
//        }

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

        $monthRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'all');
        $declinedRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'declined');
        $completedRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'completed');

        $refundRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'refund');


        $totalRepairs = 0;
        $totalDeclined = 0;
        $totalCompleted = 0;
        $totalRefund = 0;

        foreach ($monthRepairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['repairs'] += 1;
            $totalRepairs++;
        }

        foreach ($declinedRepairs as $declined) {
            $day = intval(preg_split("/[^1234567890]/", $declined['repair_date'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
        }

        foreach ($refundRepairs as $refund) {
            $day = intval(preg_split("/[^1234567890]/", $refund['repair_date'])[2]);
            $days[$day - 1]['refund'] += 1;
            $totalRefund++;
        }


        foreach ($completedRepairs as $completed) {
            $day = intval(preg_split("/[^1234567890]/", $completed['repair_date'])[2]);
            $days[$day - 1]['completed'] += 1;
            $totalCompleted++;
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

        $totalCheck = 0;
        $totalAvance = 0;
        foreach ($repairs as $repair) {
            $totalCheck += $repair->lead->issued;
        }
        foreach ($repairs as $repair) {
            $totalAvance += $repair->lead->avance;
        }

//        dd($repairs);

        return view('repair.show', compact('repairs', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalRepairs', 'nextMonthLink', 'prevMonthLink', 'declinedRepairs', 'totalDeclined', 'totalCompleted', 'totalCheck', 'totalAvance', 'totalRefund'));
    }


    public function search(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = '';
        }
        $user = Auth::user();

        if($user->isAdmin){
            $city = Session::get('city');
        }
        else{
            $city = City::where(["id" => $user->city])->first();
        }



        if ($request->query('client_fullname')) {
            $client_fullname = $request->query('client_fullname');
        } else {
            $client_fullname = '';
        }

        if ($request->query('address')) {
            $address = $request->query('address');
        } else {
            $address = '';
        }

        if ($request->query('phone')) {
            $phone = $request->query('phone');
        } else {
            $phone = '';
        }

        if ($request->query('manager_id')) {
            $manager_id = $request->query('manager_id');
        } else {
            $manager_id = 0;
        }

        if ($request->query('status')) {
            $status = $request->query('status');
        } else {
            $status = '';
        }

        if ($request->query('master_id')) {
            $master_id = $request->query('master_id');
        } else {
            $master_id = 0;
        }


        if ($user->isAdmin) {
            $temp = array();

            $suka = new Repair();
            $repairs = $suka->getResult($client_fullname, $address, $phone, $manager_id, $master_id, $status,$date);
            foreach ($repairs as $repair) {

                if ($repair->lead->city == (Session::get('city')->name)) {

                    array_push($temp, $repair);
                }
            }

            $repairs = $temp;
        } else {
            $temp = array();
            $suka = new Repair();
            $repairs = $suka->getResult($client_fullname, $address, $phone, $manager_id, $master_id, $status,$date);
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
        }


        $users = User::all();
        $managers = [];
        $masters = [];

        $totalCheck=0;
        $totalAvance=0;

        foreach ($repairs as $repair){
            if($repair->status=='completed'){
                $totalCheck+=$repair->check;
            }
            $totalAvance+=$repair->lead->avance;
        }

//        dd($totalCheck);

        foreach ($users as $user) {
            if ($user->hasRole('manager') && $user->city == $city->id) {
                array_push($managers, $user);
            } else if ($user->hasRole('master') && $user->city == $city->id) {
                array_push($masters, $user);
            }
        }


        return view('repair.search', compact('repairs', 'date',  'city', 'totalCheck', 'managers', 'masters', 'client_fullname', 'address', 'phone', 'manager_id', 'master_id', 'status', 'totalAvance'));
    }

    public function doSearch(Request $request)
    {
        $data = $request->validate([
            "client_fullname" => '',
            "address" => '',
            "phone" => '',
            "manager_id" => '',
            "master_id" => '',
            "status" => '',
        ]);

        $temp = [];
        foreach ($data as $key => $item) {
            if ($item != null) {
                $temp[$key] = $item;
            }
        }

        $query = '?';
        $index = 1;
        foreach ($temp as $key => $item) {
            if ($index == count($temp)) {
                $query = $query . $key . '=' . $item;
            } else {
                $query = $query . $key . '=' . $item . '&&';
                $index++;
            }
        }
        return redirect('/repairs/search/' . $query);
    }

    public function update(Repair $repair, Request $request)
    {
        $data = $request->all();


        if ($data['status'] == 'completed' && ($repair->status == 'declined' || $repair->status == 'in-work')) {
            $repair->check = $repair->lead->issued;
            $repair->save();
        }

        if (($data['status'] == 'declined' || $data['status'] == 'in-work') && $repair->status == 'completed') {
            $repair->check = 0;
            $repair->save();

        }


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

//        dd($data);

        $repair->documents = $repair->documents . '|' . implode('|', $documents);
        $repair->status = $data['status'];
        if (key_exists('repair_date', $data)) {
            $repair->repair_date = $data['repair_date'];
        }


        if ($data['status'] == 'completed' && $repair->lead->issued > $repair->lead->avance) {
            $state = TransactionState::getByCode('1.2.');
            $desc = 'Ремонт от ' . $repair->lead->city . ' ' . $repair->lead->address . ' ';
            $value = $repair->check - $repair->lead->avance;
            $responsible = Auth::user()->id;
            $documents = implode("|", $documents);
            $city_id = City::where(['name' => $repair->lead->city])->first()->id;
            $transaction = app(\App\Http\Controllers\TransactionController::class)->newReceipt($state->id, $desc, $value, $responsible, $city_id, $documents);
            $transaction->save();
        }
        if ($data['status'] == 'declined' && array_key_exists('refund', $data)) {
            $state = TransactionState::getByCode('1.5.');
            $desc = 'Возврат по договору ' . $repair->lead->city . ' ' . $repair->lead->address . ' ';
            $value = $repair->lead->avance;
            $responsible = Auth::user()->id;
            $documents = implode("|", $documents);
            $city_id = City::where(['name' => $repair->lead->city])->first()->id;
            $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city_id, $documents);
            $transaction->save();
        }

        $repair->save();
        return redirect()->back();
    }

    public function edit(Repair $repair)
    {
        $cities = City::all();
        $city = City::where(["name" => $repair->lead->city])->first();
        $users = User::where(["city" => $city->id])->get();

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

//        dd($data);

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

        $lead->update(["created_at" => $data['created_at'],
            "city" => $city->name,
            "subcity" => array_key_exists('subcity', $data) ? $data['subcity'] : $lead->subcity,
            "address" => $data['address'],
            "phone" => $data['phone'],
            "job_type" => $data['job_type'],
            "issued" => $data["issued"],
            "avance" => array_key_exists('avance', $data) ? $data['avance'] : $lead->avance,
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

        if (array_key_exists('master_boost', $data)) {
            $repair->master_boost = true;
        } else {
            $repair->master_boost = false;
        }

        if (array_key_exists('salary_debuff', $data)) {
            $lead->salary_debuff = true;
            $lead->save();
        } else {
            $lead->salary_debuff = false;
            $lead->save();
        }


        $repair->save();

        return (redirect()->back());
    }


    public function getMasterDaysInMonthWithWeekdays($month, $year)
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
                'successful' => 0,
                'declined' => 0,
                'workDay' => false,
                'repairs_confirmed' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getMasterMonthLeads($year, $month, $type, $master_id)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        switch ($type) {
            case 'all':
                return Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['master_id', '=', $master_id]])->get();
            case 'successful':
                return Repair::where([['status', '=', 'completed'], ['master_id', '=', $master_id]])->whereBetween('repair_date', [$startDate, $endDate])->get();
            case 'declined':
                return Repair::whereBetween('repair_date', [$startDate, $endDate])->where([['status', '=', 'declined'], ['master_id', '=', $master_id]])->get();
            default:
                return Repair::whereBetween('repair_date', [$startDate, $endDate])->get();
        }
    }


    public function masterCard(User $master, Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        if (!$master->id) {
            $master = Auth::user();
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

        $days = $this->getMasterDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);


        $monthRepairs = $this->getMasterMonthLeads($dateTemp[0], $dateTemp[1], 'all', $master->id);
        $succesfull_repairs = $this->getMasterMonthLeads($dateTemp[0], $dateTemp[1], 'successful', $master->id);
        $declined_repairs = $this->getMasterMonthLeads($dateTemp[0], $dateTemp[1], 'declined', $master->id);

        $totalRepairs = 0;
        $totalDeclined = 0;
        $totalSuccessful = 0;

        foreach ($monthRepairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['repairs'] += 1;

            $totalRepairs++;
        }

        foreach ($declined_repairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
        }

        foreach ($succesfull_repairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['successful'] += 1;
            $days[$day - 1]['repairs_confirmed'] += $repair->check;
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
        $totalConfirmed = 0;

        foreach ($days as $day) {
            if ($day['repairs_confirmed'] != 0) {
                $totalWorkDays++;
            }
            $totalConfirmed += $day['repairs_confirmed'];
        }
//        dd($monthRepairs, $master->salary($date));

        $documents = explode('|', $master->documents);

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $bonuses = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $master->id, "type" => "plus"])->get();
        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $master->id, "type" => "minus"])->get();

        return view('cards.master', compact('date', 'dateTitle', 'formattedDate', 'city', 'master', 'days', 'totalRepairs', 'nextMonthLink', 'prevMonthLink', 'totalSuccessful', 'totalDeclined', 'totalWorkDays', 'totalRepairs', 'totalConfirmed', 'documents', 'documents', 'date', 'bonuses', 'deductions'));
    }

    public function duplicate(Repair $repair)
    {
        $temp = $repair->lead;
        $newLead = Lead::where(["id" => $temp->id])->first()->replicate();
        $newLead->save();
//        dd($newLead);
        $newRepair = $repair->replicate();
        $newRepair->status = 'in-work';
        $newRepair->lead_id = $newLead->id;
        $newRepair->save();
        return redirect()->back();
    }


    public function expenseMaterialShow(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = City::where(["id" => $user->city])->first();
        if ($user->isAdmin) {
            $temp = array();

            $repairs = Repair::where([['repair_date', '=', $date]])->get();
            foreach ($repairs as $repair) {

                if ($repair->lead->city == (Session::get('city')->name)) {

                    array_push($temp, $repair);
                }
            }

            $repairs = $temp;
        } else {
            $temp = array();
            $repairs = Repair::where([['repair_date', '=', $date]])->get();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
        }

//        foreach ($repairs as $repair){
//            dd($repair->getResult('sdfs'));
//        }

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

        $monthRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'all');
        $declinedRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'declined');
        $completedRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'completed');

        $refundRepairs = $this->getMonthRepairs($dateTemp[0], $dateTemp[1], 'refund');

        $totalRepairs = 0;
        $totalDeclined = 0;
        $totalCompleted = 0;

        $totalRefund = 0;

        foreach ($monthRepairs as $repair) {
            $day = intval(preg_split("/[^1234567890]/", $repair['repair_date'])[2]);
            $days[$day - 1]['repairs'] += 1;
            $totalRepairs++;
        }

        foreach ($declinedRepairs as $declined) {
            $day = intval(preg_split("/[^1234567890]/", $declined['repair_date'])[2]);
            $days[$day - 1]['declined'] += 1;
            $totalDeclined++;
        }

        foreach ($completedRepairs as $completed) {
            $day = intval(preg_split("/[^1234567890]/", $completed['repair_date'])[2]);
            $days[$day - 1]['completed'] += 1;
            $totalCompleted++;
        }

        foreach ($refundRepairs as $refund) {
            $day = intval(preg_split("/[^1234567890]/", $refund['repair_date'])[2]);
            $days[$day - 1]['refund'] += 1;
            $totalRefund++;
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

        $totalCheck = 0;
        foreach ($repairs as $repair) {
            $totalCheck += $repair->check;
        }

        return view('repair.expense', compact('repairs', 'date', 'dateTitle', 'formattedDate', 'city', 'days', 'totalRepairs', 'nextMonthLink', 'prevMonthLink', 'declinedRepairs', 'totalDeclined', 'totalCompleted', 'totalCheck'));
    }


}
