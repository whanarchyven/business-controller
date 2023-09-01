<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Expense;
use App\Models\Lead;
use App\Models\ManagerCoordinator;
use App\Models\Nomenclature;
use App\Models\NomenclatureExpense;
use App\Models\NomenclatureReceipt;
use App\Models\Plan;
use App\Models\Receipt;
use App\Models\Repair;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class DirectorController extends Controller
{
    public function getManagers($city_id)
    {
        $director = Auth::user();
        $temp = User::where([['city', '=', $city_id]])->get();

        $managers = array();

        foreach ($temp as $record) {
            if ($record->hasRole('manager')) {
                array_push($managers, $record);
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
        return redirect(route('director.managers'));
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
        return redirect()->route('director.managers');
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

        $date = Carbon::now()->toDateString();
        $yearTemp = preg_split("/[^1234567890]/", $date)[0];
        $monthTemp = preg_split("/[^1234567890]/", $date)[1];

        $plan = Plan::where([['year', '=', $yearTemp], ['month', '=', $monthTemp], ['city_id', '=', $city_id]])->first();


        return view('roles.coordinator.control', compact('cities', 'managers', 'city_id', 'leads', 'declined', 'month', 'products_selled', 'todayLeads', 'todayProductsSelled', 'todayDeclined', 'plan', 'city_id'));
    }

    public function manageLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();

        $lead->update(["manager_id" => $manager->id, "status" => 'managed']);
        $manager->status = 'meeting-managed';
        $manager->save();

        return redirect(route('director.managers'));

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

    public function changePlan(Request $request)
    {
        $data = $request->all();
        $new_plan = $data['value'];
        $date = Carbon::now()->toDateString();
        $yearTemp = preg_split("/[^1234567890]/", $date)[0];
        $monthTemp = preg_split("/[^1234567890]/", $date)[1];


        if ($data && $data['city']) {
            $city_id = $data['city'];
        } else {
            $city_id = Auth::user()->city;
        }


        $plan = Plan::firstOrCreate([['year', '=', $yearTemp], ['month', '=', $monthTemp], ['city_id', '=', $city_id]], ['year' => $yearTemp, 'month' => $monthTemp]);
        $plan->value = $new_plan;
        $plan->city_id = $city_id;

        $plan->save();

        return redirect()->back();
    }

    public function getMonthWorkLeads($year, $month)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return Lead::whereBetween('created_at', [$startDate, $endDate])->where([['status', '=', 'in-work']])->get();
    }

    public function daily(Request $request)
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

        if (intval($dateTemp[1]) + 1 < 10) {
            $nextMonthLink = $dateTemp[0] . ('-0' . (intval($dateTemp[1]) + 1)) . '-01';
        } else {
            if (intval($dateTemp[1]) + 1 > 12) {
                $nextMonthLink = intval($dateTemp[0]) + 1 . '-01' . '-01';
            } else {
                $nextMonthLink = $dateTemp[0] . ('-' . (intval($dateTemp[1]) + 1)) . '-01';
            }
        }

        if (intval($dateTemp[1]) - 1 >= 10) {
            $prevMonthLink = $dateTemp[0] . ('-' . (intval($dateTemp[1]) - 1)) . '-01';
        } else {
            if (intval(intval($dateTemp[1]) - 1 <= 0)) {
                $prevMonthLink = intval($dateTemp[0]) - 1 . '-12' . '-01';
            } else {
                $prevMonthLink = $dateTemp[0] . ('-0' . (intval($dateTemp[1]) - 1)) . '-01';
            }
        }

        $leads = $this->getMonthWorkLeads($dateTemp[0], $dateTemp[1]);


        return (view('roles.director.daily', compact('dateTitle', 'prevMonthLink', 'nextMonthLink', 'leads')));
    }

    public function acceptLeadView(Lead $lead)
    {
        $city = City::where(["name" => $lead->city])->first();

        $managersTemp = User::where([['city', '=', $city->id]])->get();

        $managers = array();

        foreach ($managersTemp as $manager) {
            if ($manager->hasRole('manager')) {
                array_push($managers, $manager);
            }
        }


        return view('roles.director.accept', compact('lead', 'managers'));
    }

    public function closeLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $documents = array();
        if ($files = $request->file('documents')) {
            $i = 1;
            foreach ($files as $file) {
                $name = Carbon::now()->toDateString() . '-' . $lead->client_fullname . '-' . $lead->city . '-' . $i . '.' . $file->extension();
//                $name = Carbon::now()->toDateString() . '-' . preg_split("/[\s,]+/", $lead['client_fullname'])[0] . '-' . $i . '.' . $file->extension()
                $file->move('documents', $name);
                $documents[] = $name;
                $i++;
            }
        }

        $suka = Lead::whereDate("created_at", Carbon::today())->where(["manager_id" => $lead->getManagerId->id, "status" => 'completed'])->first();

        if ($suka == null) {
            $lead->getManagerId->salary($lead->getManagerId->bet);
        }

        //здесь будем бонусы выписывать

        $lead->update(["issued" => $data['issued'], "avance" => $data['avance'], "documents" => implode("|", $documents), "status" => 'completed']);


        $repair = new Repair();
        $repair->lead_id = $lead->id;
        $repair->check = $data['issued'];
        $repair->repair_date = $data['repair_date'];
        $repair->save();


        return redirect(route('director.daily'));
    }


    public function closeLeadNull(Lead $lead)
    {

        $lead->status = 'declined';
        $lead->save();

        return redirect(route('director.daily'));
    }

    public function nomenclature()
    {
        $nomenclature = Nomenclature::all();

        return (view('nomenclature.show', compact('nomenclature')));
    }

    public function addNomenclature()
    {
        return (view('nomenclature.new'));
    }

    public function editNomenclature(Nomenclature $nomenclature)
    {
        return (view('nomenclature.edit', compact('nomenclature')));
    }

    public function updateNomenclature(Nomenclature $nomenclature, Request $request)
    {
        $data = $request->validate([
            'name' => '',
            'unit' => '',
            'price' => '',
        ]);
        $nomenclature->update($data);

        return (redirect(route('director.nomenclature')));
    }

    public function storeNomenclature(Request $request)
    {
        $data = $request->validate([
            'name' => '',
            'unit' => '',
            'price' => ''
        ]);
        $nomenclature = new Nomenclature($data);
        $nomenclature->remain = 0;
        $nomenclature->save();

        return (redirect(route('director.nomenclature')));
    }

    public function receipt()
    {
        $nomenclature = Nomenclature::all();
        return view('roles.director.receipt', compact('nomenclature'));
    }

    public function newReceipt(Request $request)
    {
        $date = $request->all();

        $receipt = new Receipt();
        $receipt->author = Auth::user()->id;
        $receipt->save();

        $date = array_slice($date, 2, count($date));

        for ($i = 1; $i <= count($date) / 2; $i++) {
            $nomenclature_receipt = new NomenclatureReceipt();
            $nomenclature_receipt->quantity = $date['quantity' . $i];
            $nomenclature_receipt->nomenclature_id = $date['nomenclature' . $i];
            $nomenclature_receipt->receipt_id = $receipt->id;

            $nomenclature_receipt->save();

            $plus = Nomenclature::where(["id" => $date['nomenclature' . $i]])->first();
            $plus->remain += $date['quantity' . $i];
            $plus->save();
        }
        return (redirect(route('director.nomenclature')));
    }

    public function expense()
    {
        $repairs = Repair::all();
        return view('roles.director.expense', compact('repairs'));
    }

    public function newExpense(Repair $repair)
    {
        $nomenclature = Nomenclature::all();
        return view('roles.director.expense_new', compact('nomenclature', 'repair'));
    }

    public function expenseStore(Repair $repair, Request $request)
    {
        $date = $request->all();

        $expense = new Expense();
        $expense->author = $repair->master ? $repair->master->id : Auth::user()->id;
        $expense->repair_id = $repair->id;
        $expense->save();

        $date = array_slice($date, 2, count($date));

        for ($i = 1; $i <= count($date) / 2; $i++) {
            $nomenclature_expense = new NomenclatureExpense();
            $nomenclature_expense->quantity = $date['quantity' . $i];
            $nomenclature_expense->nomenclature_id = $date['nomenclature' . $i];
            $nomenclature_expense->expense_id = $expense->id;

            $nomenclature_expense->save();

            $minus = Nomenclature::where(["id" => $date['nomenclature' . $i]])->first();
            $minus->remain = $minus->remain - $date['quantity' . $i];
            $minus->save();
        }
        return (redirect(route('director.nomenclature')));
    }

    public function newUserView()
    {
        $cities = City::all();

        $users = User::where(["city" => Auth::user()->city])->get();

        $coordinators = array();

        foreach ($users as $user) {
            if ($user->hasRole('coordinator')) {
                array_push($coordinators, $user);
            }
        }


        return view('roles.director.employer.new', compact('cities', 'coordinators'));
    }

    public function storeNewUser(Request $request)
    {

        $data = $request->all();
        $documents = array();
        if ($files = $request->file('documents')) {
            $i = 1;
            foreach ($files as $file) {
                $name = Carbon::now()->toDateString() . '-' . $data['name'] . $i . '.' . $file->extension();
//                $name = Carbon::now()->toDateString() . '-' . preg_split("/[\s,]+/", $repair->lead['client_fullname'])[0] . '-' . $i . '.' . $file->extension()
                $file->move('documents', $name);
                $documents[] = $name;
                $i++;
            }
        }


        $operator = Role::where('slug', 'operator')->first();
        $manager = Role::where('slug', 'manager')->first();
        $master = Role::where('slug', 'master')->first();
        $coordinator = Role::where('slug', 'coordinator')->first();
        $director = Role::where('slug', 'director')->first();
        $admin = Role::where('slug', 'admin')->first();


        $newUser = new User();
        $newUser->name = $data['name'];
        $newUser->email = $data['email'];
        $newUser->password = bcrypt($data['password']);
        $newUser->city = Auth::user()->city;
        $newUser->status = 'free';
        $newUser->salary = 0;
        $newUser->documents = implode('|', $documents);
        $newUser->bet = $data['bet'];
        $newUser->save();
        switch ($data['role']) {
            case 'operator':
                $newUser->roles()->attach($operator);
                break;
            case 'manager':
                $newUser->roles()->attach($manager);
                $manager_coordinator = new ManagerCoordinator();
                $manager_coordinator->manager_id = $newUser->id;
                $manager_coordinator->coordinator_id = $data['coordinator_id'];
                $manager_coordinator->save();
                break;
            case 'coordinator':
                $newUser->roles()->attach($coordinator);
                break;
        }
        return redirect()->back();
    }
}
