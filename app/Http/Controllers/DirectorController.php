<?php

namespace App\Http\Controllers;

use App\Models\BonusManager;
use App\Models\City;
use App\Models\CoordinatorCity;
use App\Models\DirectorWorkday;
use App\Models\EmployeerWorkDay;
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
use App\Models\Transaction;
use App\Models\TransactionState;
use App\Models\User;
use Carbon\Carbon;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use GuzzleHttp;

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
        $startDate = Carbon::createFromDate($date)->startOfMonth();
        $endDate = Carbon::createFromDate($date)->endOfMonth();
        return Lead::whereBetween('meeting_date', [$startDate->toDateString(), $endDate->toDateString()])->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', '=', $city]])->get();

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
        return Lead::whereDate('created_at', '=', Carbon::now()->toDateString())->where([['status', $isDeclined ? '=' : '!=', 'declined'], ['city', '=', $city]])->get();
    }

    public function declineLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $lead->update(['status' => 'declined', 'note' => $data['note']]);
        $manager = $lead->getManagerId;
        if ($manager){
            $manager->status = 'free';
            $this->changeBotPhoto($manager,'free');
            $manager->save();
        }
        return redirect()->back();
    }

    public function update(Lead $lead)
    {
        $data = \request()->all();
        $user = Auth::user()->id;
//        $data['status'] = 'not-managed';

        if (isset($data['measuring'])) {
            $data['measuring'] == 'on' ? $data['measuring'] = true : $data['measuring'] = false;
        }

        if (isset($data['range'])) {
            $data['range'] == 'on' ? $data['range'] = true : $data['range'] = false;
        }

        if (array_key_exists('check',$data)&&$data['check']!=$lead->check){
            $data['status']='in-work';
        }

//        dd($data);
        $lead->update($data);
        return redirect()->route('director.managers');
    }


    public function controlTable(Request $request)
    {

        $data = $request->all();
        $user = Auth::user();
        if ($data && $data['city']) {
            $city_id = $data['city'];
        } else {
            if ($user->isAdmin) {
                $city_id = Session::get('city')->id;
            } else {
                $city_id = Auth::user()->city;
            }
        }

        $city = City::where([['id', '=', $city_id]])->first()->name;

        $leads = $this->getMonthLeads(false, $city);
        $declined = $this->getMonthLeads(true, $city);
        $month = $this->getMonth();
//        dd($leads);

        $products_selled = 0;
        $products_issued = 0;
        $meetings=0;

        $repairs = array();

        foreach ($leads as $lead) {
            $products_selled += $lead->check;
            if($lead->entered){
                $meetings++;
            }
            if ($lead->repair && $lead->repair->status == 'completed') {
                $products_issued += $lead->repair->check;
                array_push($repairs, $lead->repair);
            }
        }

//        dd($repairs);

        $todayLeads = $this->getTodayLeads(false, $city);
        $todayDeclined = $this->getTodayLeads(true, $city);


        $todayProductsSelled = 0;
        $todayProductsIssued = 0;
        $todayMeetings = 0;

        foreach ($todayLeads as $lead) {
            $todayProductsSelled += $lead->check;
            if($lead->entered){
                $todayMeetings++;
            }
            if ($lead->repair && $lead->repair->status == 'completed') {
                $todayProductsIssued += $lead->repair->check;
            }
        }

        $cities = $this->getCities();
        $managers = $this->getManagers($city_id);
        $managers_leads=[];
        foreach ($managers as $manager){
            $managers_leads[$manager->name]=0;
            foreach ($todayLeads as $todayLead){
                if ($todayLead->manager_id==$manager->id&&$todayLead->entered){
                    $managers_leads[$manager->name]+=$todayLead->issued;
                }
            }
        }

        $date = Carbon::now()->toDateString();
        $yearTemp = preg_split("/[^1234567890]/", $date)[0];
        $monthTemp = preg_split("/[^1234567890]/", $date)[1];

        $plan = Plan::where([['year', '=', $yearTemp], ['month', '=', $monthTemp], ['city_id', '=', $city_id]])->first();


        return view('roles.coordinator.control', compact('cities', 'managers', 'city_id', 'leads', 'declined', 'month', 'products_selled', 'todayLeads', 'todayProductsSelled', 'todayDeclined', 'plan', 'city_id', 'user', 'products_issued', 'todayProductsIssued', 'todayMeetings','meetings','managers_leads'));
    }

    public function manageLead(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();

        $lead->update(["manager_id" => $manager->id, "status" => 'managed']);
        $this->changeBotPhoto($manager,'meeting-managed');
        $manager->status = 'meeting-managed';
        $manager->save();

        $jobTypes = [
            "1" => 'Окна', "2" => 'Конструкции ПВХ', "3" => 'Многопрофиль', "4" => 'Электрика',
        ];

        if ($manager->chat_bot_id) {
            $client = new GuzzleHttp\Client();
//            $res = $client->get('https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/sendMessage?chat_id=' . $manager->chat_bot_id . '&parse_mode=html&text=<b>Новая заявка для ' . $manager->name . '</b>%0A' . '<b>ФИО клиента: </b>' . $lead->client_fullname . '%0A' . '<b>Адрес: </b>' . $lead->address . '%0A' . '<b>Ожидает: </b>' . $lead->time_period . '%0A' . '<b>Тип работ: </b>' . $jobTypes[$lead->job_type] . '%0A' . '<b>Замер: </b>' . ($lead->range ? 'Нет' : 'Да') . '%0A' . '<b>Диапазон : </b>' . ($lead->measuring ? 'Нет' : 'Да') . '%0A' . '<b>Комментарий: </b>' . $lead->comment . '%0A' . '<b>Примечание: </b>' . $lead->note . '%0A');
            $res = $client->get('https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/sendMessage?chat_id=' . $manager->chat_bot_id . '&parse_mode=html&text=<b>Новая заявка для ' . $manager->name . '</b>%0A' . '<b>ФИО клиента: </b>' . $lead->client_fullname . '%0A' . '<b>Адрес: </b>' . $lead->address . '%0A' . '<b>Ожидает: </b>' . $lead->time_period . '%0A' . '<b>Тип работ: </b>' . $jobTypes[$lead->job_type] . '%0A' . '<b>Комментарий: </b>' . $lead->comment . '%0A');
        }


        if (Auth::user()->hasRole('coordinator')) {
            return redirect(route('coordinator.managers'));
        } else {
            return redirect(route('director.managers'));
        }

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

    public function changeManager(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();
        $lead->update(["manager_id" => null, "accepted" => null, "entered" => null,]);
        $this->changeBotPhoto($manager,'free');
        $manager->status = 'free';
        $manager->save();
        $lead->save();
        return redirect()->back();
    }

    public function sendPhone(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();
        if ($manager->chat_bot_id) {
            $client = new GuzzleHttp\Client();
            $res = $client->get('https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/sendMessage?chat_id=' . $manager->chat_bot_id . '&parse_mode=html&text=' .'<b>ФИО клиента: </b>' . $lead->client_fullname . '%0A' . '<b>Телефон: </b>' . $lead->phone .'%0A');
        }
        return redirect()->back();
    }

    public function sendAddress(Lead $lead, Request $request)
    {
        $data = $request->all();
        $manager = User::where([['id', '=', $data['manager']]])->first();
        if ($manager->chat_bot_id) {
            $client = new GuzzleHttp\Client();
            $res = $client->get('https://api.telegram.org/bot6384276235:AAEGyfBmhCSgizgLa3_vRbZ1VSFcPtYZAHk/sendMessage?chat_id=' . $manager->chat_bot_id . '&parse_mode=html&text=' .'<b>ФИО клиента: </b>' . $lead->client_fullname . '%0A' . '<b>Адрес: </b>' . $lead->address .'%0A');
        }
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

    public function getMonthWorkLeads($date)
    {

        $city = City::where(["id" => Auth::user()->city])->first();

//        dd(Session::get('city')->name);

        if (Auth::user()->isAdmin) {
            return Lead::whereDate('created_at', $date)->where([['status', '=', 'in-work'], ['city', '=', Session::get('city')->name]])->get()->reverse();
        } else {
            return Lead::whereDate('created_at', $date)->where([['status', '=', 'in-work'], ['city', '=', $city->name]])->get()->reverse();
        }
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
                $dateTitle = ' Января';
                break;
            case '02':
                $dateTitle = ' Февраля ';
                break;
            case '03':
                $dateTitle = ' Марта ';
                break;
            case '04':
                $dateTitle = ' Апреля ';
                break;
            case '05':
                $dateTitle = ' Мая ';
                break;
            case '06':
                $dateTitle = ' Июня ';
                break;
            case '07':
                $dateTitle = ' Июля ';
                break;
            case '08':
                $dateTitle = ' Августа ';
                break;
            case '09':
                $dateTitle = ' Сентября ';
                break;
            case '10':
                $dateTitle = ' Октября ';
                break;
            case '11':
                $dateTitle = ' Ноября ';
                break;
            case '12':
                $dateTitle = ' Декабря ';
                break;
        }
        $dateTitle = intval($dateTemp[2]) . $dateTitle . $dateTemp[0];

//        if (intval($dateTemp[1]) + 1 < 10) {
//            $nextMonthLink = $dateTemp[0] . ('-0' . (intval($dateTemp[1]) + 1)) . '-01';
//        } else {
//            if (intval($dateTemp[1]) + 1 > 12) {
//                $nextMonthLink = intval($dateTemp[0]) + 1 . '-01' . '-01';
//            } else {
//                $nextMonthLink = $dateTemp[0] . ('-' . (intval($dateTemp[1]) + 1)) . '-01';
//            }
//        }
//
//        if (intval($dateTemp[1]) - 1 >= 10) {
//            $prevMonthLink = $dateTemp[0] . ('-' . (intval($dateTemp[1]) - 1)) . '-01';
//        } else {
//            if (intval(intval($dateTemp[1]) - 1 <= 0)) {
//                $prevMonthLink = intval($dateTemp[0]) - 1 . '-12' . '-01';
//            } else {
//                $prevMonthLink = $dateTemp[0] . ('-0' . (intval($dateTemp[1]) - 1)) . '-01';
//            }
//        }
//        dd(Carbon::today()->toDateString());
        $prevMonthLink = Carbon::createFromDate($date)->subDays(1)->toDateString();
        $nextMonthLink = Carbon::createFromDate($date)->addDays(1)->toDateString();
        $leads = $this->getMonthWorkLeads($date);
//        dd($leads);

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


        //здесь будем бонусы выписывать




        $repair = new Repair();
        $repair->lead_id = $lead->id;
        $repair->check = 0;
        $repair->repair_date = $data['repair_date'];
        $repair->save();

        $lead->update(["issued" => $data['issued'], "avance" => $data['avance'], "documents" => implode("|", $documents), "status" => 'completed']);

        if ($data['avance'] && $data['avance'] > 0) {
            $state = TransactionState::getByCode('1.1.');
            $desc = 'Предоплата от ' . $lead->city . ' ' . $lead->address . ' ';
            $value = $data['avance'];
            $responsible = $lead->getManagerId->id;
            $documents = implode("|", $documents);
            $city_id = City::where(['name' => $lead->city])->first()->id;
            $transaction = app(\App\Http\Controllers\TransactionController::class)->newReceipt($state->id, $desc, $value, $responsible, $city_id, $documents);
        }


        if ($data['issued'] != 0 && DirectorWorkday::whereDate('created_at', $lead->created_at)->where(["director_id" => Auth::user()->id])->first() == null) {
            $director_workday = new DirectorWorkday(["director_id" => Auth::user()->id]);
            $director_workday->save();
        }

        if (EmployeerWorkDay::whereDate('created_at', $lead->created_at)->where(["user_id" => $lead->getManagerId->id])->first() == null) {
            $workDay = new EmployeerWorkDay(["user_id" => $lead->getManagerId->id]);
            $workDay->save();
        }

        $managerTodayLeads = Lead::where(["manager_id" => $lead->getManagerId->id])->whereDate('created_at', $lead->meeting_date)->get();
        $totalCheckToday = 0;

        foreach ($managerTodayLeads as $managerTodayLead) {
            $totalCheckToday += $managerTodayLead->issued;
        }

        if($lead->check>=15000&&$lead->avance>=$lead->check/2){
            $newBonus = new BonusManager(["user_id" => $lead->getManagerId->id, "type" => "plus", "amount" => 500, "reason" => "Бонус за 15000 " . $lead->meeting_date, "city_id" => $lead->getManagerId->city]);
            $newBonus->save();
        }

//        if ($totalCheckToday >= 15000 && BonusManager::where(["user_id" => $lead->getManagerId->id, "reason" => 'Бонус за 15000 ' . $lead->meeting_date])->first() == null) {
//            $newBonus = new BonusManager(["user_id" => $lead->getManagerId->id, "type" => "plus", "amount" => 500, "reason" => "Бонус за 15000 " . $lead->meeting_date, "city_id" => $lead->getManagerId->city]);
//            $newBonus->save();
//        }
        if ($totalCheckToday >= 50000 && BonusManager::where(["user_id" => $lead->getManagerId->id, "reason" => 'Бонус за 50000 ' . $lead->meeting_date])->first() == null) {
            $newBonus = new BonusManager(["user_id" => $lead->getManagerId->id, "type" => "plus", "amount" => 500, "reason" => "Бонус за 50000 " . $lead->meeting_date, "city_id" => $lead->getManagerId->city]);
            $newBonus->save();
        }
        if ($totalCheckToday >= 100000 && BonusManager::where(["user_id" => $lead->getManagerId->id, "reason" => 'Бонус за 100000 ' . $lead->meeting_date])->first() == null) {
            $newBonus = new BonusManager(["user_id" => $lead->getManagerId->id, "type" => "plus", "amount" => 500, "reason" => "Бонус за 100000 " . $lead->meeting_date, "city_id" => $lead->getManagerId->city]);
            $newBonus->save();
        }

        return redirect(route('director.daily').'?date='.Carbon::createFromDate($lead->created_at)->toDateString());
    }


    public function closeLeadNull(Lead $lead)
    {

        $lead->status = 'declined';
        $lead->issued = 0;
        $lead->save();

        return redirect(route('director.daily').'?date='.Carbon::createFromDate($lead->created_at)->toDateString());
    }

    public function nomenclature()
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $nomenclature = Nomenclature::where(["city_id" => Session::get('city')->id])->get();
        } else {
            $nomenclature = Nomenclature::where(["city_id" => $user->city])->get();
        }
        return (view('nomenclature.show', compact('nomenclature', 'user')));
    }

    public function addNomenclature()
    {
        $user = Auth::user();
        return (view('nomenclature.new', compact('user')));
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
            'price' => '',
            'city_id' => ''
        ]);
//        dd($data);
        $nomenclature = new Nomenclature($data);
        $nomenclature->remain = 0;
        $nomenclature->city_id = $data['city_id'];
        $nomenclature->save();

        return (redirect(route('director.nomenclature')));
    }

    public function receipt()
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $nomenclature = Nomenclature::where(["city_id" => Session::get('city')->id])->get();
        } else {
            $nomenclature = Nomenclature::where(["city_id" => $user->city])->get();
        }
        return view('roles.director.receipt', compact('nomenclature', 'user'));
    }

    public function newReceipt(Request $request)
    {
        $date = $request->all();
        $receipt = new Receipt();
        $receipt->author = Auth::user()->id;
        $receipt->save();

        $date = array_slice($date, 2, count($date));

        $totalPrice=0;
        $totalCart='';

        for ($i = 1; $i <= count($date) / 2; $i++) {
            $nomenclature_receipt = new NomenclatureReceipt();
            $nomenclature_receipt->quantity = $date['quantity' . $i];
            $nomenclature_receipt->nomenclature_id = $date['nomenclature' . $i];
            $nomenclature_receipt->receipt_id = $receipt->id;

            $nomenclature_receipt->save();

            $plus = Nomenclature::where(["id" => $date['nomenclature' . $i]])->first();
            $plus->remain += $date['quantity' . $i];
            $totalPrice+=($plus->price*$date['quantity'.$i]);
            $totalCart.=$plus->name.' '.$date['quantity'.$i].' '.$plus->unit.', ';
            $city=City::where(["id"=>$plus->city_id])->first();
            $plus->save();
        }

        $state = TransactionState::getByCode('2.02.2.');
        $desc = 'Закупка материала ' . $city->name.': '.$totalCart;
        $value = $totalPrice;
        $responsible = Auth::user()->id;
        $documents = '';
        $city_id = $city->id;
        $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city_id, $documents);
        return (redirect(route('director.nomenclature')));
    }

    public function expense()
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $repairs = Repair::where([['status','!=','completed'],['status','!=','declined']])->get();
            $temp = array();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == Session::get('city')->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
        } else {
            $city = City::where(["id" => $user->city])->first();
            $repairs = Repair::where([['status','!=','completed'],['status','!=','declined']])->get();
            $temp = array();
            foreach ($repairs as $repair) {
                if ($repair->lead->city == $city->name) {
                    array_push($temp, $repair);
                }
            }
            $repairs = $temp;
        }
        return view('roles.director.expense', compact('repairs'));
    }

    public function newExpense(Repair $repair)
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $nomenclature = Nomenclature::where(["city_id" => Session::get('city')->id])->get();
        } else {
            $nomenclature = Nomenclature::where(["city_id" => $user->city])->get();
        }
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
        return (redirect(route('director.expense')));
    }

    public function declineExpense(Repair $repair, Request $request)
    {
        $id=$repair->materials[0]->expense_id;
        foreach ($repair->materials as $material){
//            dd($material);
            $minus = Nomenclature::where(["id" => $material->nomenclature_id])->first();
            $minus->remain = $minus->remain + $material->quantity;
            $minus->save();
            $material->delete();
//            dd('deleted');
        }
        $expense=Expense::where(['id'=>$id])->delete(); //Удаляем expense
        return redirect()->back();
    }


    public function managersView(Request $request)
    {
        $director = Auth::user();
        $title = 'Менеджеры';
        $role = 'Менеджер';
        $link = 'managers';
        $route_card = 'director.managercard';
        if ($director->isAdmin) {
            $city = $request->session()->get('city')->id;
        } else {
            $city = $director->city;
        }
        $city = City::where(["id" => $city])->first();

        $cities = City::all();
        $users = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($users as $user) {
            if ($user->hasRole('manager')) {
                array_push($temp, $user);
            }
        }
        $users = $temp;

        $dismissed = User::onlyTrashed()->where(["city" => $city->id])->get();
        $dismissed_temp = array();

        foreach ($dismissed as $dis) {
            if ($dis->hasRole('manager')) {
                array_push($dismissed_temp, $dis);
            }
        }
        $dismissed = $dismissed_temp;


        return view('roles.director.employer.show', compact('users', 'city', 'title', 'director', 'cities', 'link', 'role', 'route_card', 'dismissed'));
    }

    public function operatorsView(Request $request)
    {
        $director = Auth::user();
        $title = 'Операторы';
        $role = 'Оператор';
        $link = 'operator';
        $route_card = 'director.operatorcard';

        if ($director->isAdmin) {
            $city = $request->session()->get('city')->id;
        } else {
            $city = $director->city;
        }
        $city = City::where(["id" => $city])->first();

        $cities = City::all();
        $users = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($users as $user) {
            if ($user->hasRole('operator')) {
                array_push($temp, $user);
            }
        }
        $users = $temp;


        $dismissed = User::onlyTrashed()->where(["city" => $city->id])->get();
        $dismissed_temp = array();

        foreach ($dismissed as $dis) {
            if ($dis->hasRole('operator')) {
                array_push($dismissed_temp, $dis);
            }
        }
        $dismissed = $dismissed_temp;
        return view('roles.director.employer.show', compact('users', 'city', 'title', 'director', 'cities', 'link', 'role', 'route_card', 'dismissed'));
    }

    public function coordinatorsView(Request $request)
    {
        $director = Auth::user();
        $title = 'Координаторы';
        $role = 'Координатор';
        $link = 'coordinator';
        $route_card = 'director.coordinatorcard';

        if ($director->isAdmin) {
            $city = $request->session()->get('city')->id;
        } else {
            $city = $director->city;
        }
        $city = City::where(["id" => $city])->first();

        $cities = City::all();
        $users = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($users as $user) {
            if ($user->hasRole('coordinator')) {
                array_push($temp, $user);
            }
        }
        $users = $temp;

        $dismissed = User::onlyTrashed()->where(["city" => $city->id])->get();
        $dismissed_temp = array();

        foreach ($dismissed as $dis) {
            if ($dis->hasRole('coordinator')) {
                array_push($dismissed_temp, $dis);
            }
        }
        $dismissed = $dismissed_temp;
        return view('roles.director.employer.show', compact('users', 'city', 'title', 'director', 'cities', 'link', 'role', 'route_card', 'dismissed'));
    }


    public function mastersView(Request $request)
    {
        $director = Auth::user();
        $title = 'Мастера';
        $role = 'Мастер';
        $link = 'masters';
        $route_card = 'director.mastercard';

        if ($director->isAdmin) {
            $city = $request->session()->get('city')->id;
        } else {
            $city = $director->city;
        }
        $city = City::where(["id" => $city])->first();

        $cities = City::all();
        $users = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($users as $user) {
            if ($user->hasRole('master')) {
                array_push($temp, $user);
            }
        }
        $users = $temp;
        $dismissed = User::onlyTrashed()->where(["city" => $city->id])->get();
        $dismissed_temp = array();

        foreach ($dismissed as $dis) {
            if ($dis->hasRole('master')) {
                array_push($dismissed_temp, $dis);
            }
        }
        $dismissed = $dismissed_temp;
        return view('roles.director.employer.show', compact('users', 'city', 'title', 'director', 'cities', 'link', 'role', 'route_card', 'dismissed'));
    }

    public function directorsView(Request $request)
    {
        $director = Auth::user();
        $title = 'Руководители';
        $role = 'Руководитель';
        $link = 'directors';
        $route_card = 'director.directorcard';

        if ($director->isAdmin) {
            $city = $request->session()->get('city')->id;
        } else {
            $city = $director->city;
        }
        $city = City::where(["id" => $city])->first();

        $cities = City::all();
        $users = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($users as $user) {
            if ($user->hasRole('director') && !$user->isAdmin) {
                array_push($temp, $user);
            }
        }
        $users = $temp;
        $dismissed = User::onlyTrashed()->where(["city" => $city->id])->get();
        $dismissed_temp = array();

        foreach ($dismissed as $dis) {
            if ($dis->hasRole('director')) {
                array_push($dismissed_temp, $dis);
            }
        }
        $dismissed = $dismissed_temp;
        return view('roles.director.employer.show', compact('users', 'city', 'title', 'director', 'cities', 'link', 'role', 'route_card', 'dismissed'));
    }


    public function newUserView()
    {
        $cities = City::all();

        $director = Auth::user();

        if(Auth::user()->isAdmin){
            $users = User::where(["city" => Session::get('city')->id])->get();
        }
        else{
            $users = User::where(["city" => Auth::user()->city])->get();
        }

        $coordinators = array();
        $managers=array();

        foreach ($users as $user) {
            if ($user->hasRole('coordinator')) {
                array_push($coordinators, $user);
            }
            if($user->hasRole('manager')){
                array_push($managers,$user);
            }
        }


        return view('roles.director.employer.new', compact('cities', 'coordinators', 'managers', 'director'));
    }

    public function storeNewUser(Request $request)
    {

        $data = $request->all();

//        dd($data);

        if (User::where(["email" => $data['email']])->exists()) {
            return redirect()->back()->with('error', 'Пользователь с таким логином уже существует! Придумайте другой');
        }

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


        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < 8; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        $newUser = new User();
        $newUser->name = $data['name'];
        $newUser->email = $data['email'];
        $newUser->birth_date = $data['birth_date'];
        $newUser->password = bcrypt($result);
        $newUser->city = array_key_exists('city', $data) ? $data['city'] : Auth::user()->city;
        $newUser->mentor_id=array_key_exists('mentor_id',$data)?$data['mentor_id']:null;
        $newUser->status = 'free';
        $newUser->salary = 0;
        $newUser->documents = implode('|', $documents);
//        $newUser->bet = $data['bet'] ? $data['bet'] : 0;
        $newUser->phone = $data['phone'];
        $newUser->isAdmin = 0;
        $newUser->save();
        switch ($data['role']) {
            case 'operator':
                $newUser->roles()->attach($operator);
                break;
            case 'manager':
                $newUser->roles()->attach($manager);
                break;
            case 'coordinator':
                $newUser->roles()->attach($coordinator);
                $cities = City::all();
                foreach ($cities as $city) {
                    if (array_key_exists(str_replace(' ', '_', $city->name), $data)) {
                        $coordCity = new CoordinatorCity(['user_id' => $newUser->id, "city_id" => $city->id]);
                        $coordCity->save();
                    }
                }
//                dd(count($cities));
                break;
            case 'director':
                $newUser->roles()->attach($director);
                break;
            case 'master':
                $newUser->roles()->attach($master);
                break;
        }
        return redirect()->back()->with('success', 'Пользователь успешно зарегистрирован, пароль: ' . $result,);
    }

    public function updateUserView(User $user)
    {
        $director = Auth::user();
        if (Auth::user()->isAdmin) {
            $cities = City::all();
        } else {
            $cities = City::where(["id" => Auth::user()->city])->first();
        }
        return view('roles.director.employer.edit', compact('user', 'cities', 'director'));
    }

    public function updateUser(User $user, Request $request)
    {
        $data = $request->all();

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < 8; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        $user->name = $data['name'] ? $data['name'] : $user->name;
        $user->email = $data['email'] ? $data['email'] : $user->email;
        $user->birth_date = $data['birth_date'] ? $data['birth_date'] : $user->birth_date;
        $user->city = $data['city'] ? $data['city'] : $user->city;
        $user->phone = $data['phone'] ? $data['phone'] : $user->phone;
        if($user->hasRole('manager')){
            $user->chat_bot_id = $data['chat_bot_id'] ? $data['chat_bot_id'] : $user->chat_bot_id;
        }
        $user->password = bcrypt($result);
        if(Auth::user()->isAdmin&&$user->hasRole('coordinator')){
            CoordinatorCity::where(["user_id"=>$user->id])->delete();
            $cities = City::all();
            foreach ($cities as $city) {
                if (array_key_exists(str_replace(' ', '_', $city->name), $data)) {
                    $coordCity = new CoordinatorCity(['user_id' => $user->id, "city_id" => $city->id]);
                    $coordCity->save();
                }
            }

        }
        $user->save();


        return redirect()->back()->with('success', 'Пользователь успешно обновлён, пароль: ' . $result);
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->back();
    }

    public function restoreUser(Request $request)
    {
        $data = $request->all();
//        dd($data);
        User::where(["id" => $data['user']])->withTrashed()->restore();
        return redirect()->back();
    }


    public function changeCity(City $city, Request $request)
    {
        $request->session()->put('city', $city);
        return redirect()->back();
    }

    public function getCity(Request $request)
    {
        dd($request->session()->get('city'));
    }


    public function getTransactionsView(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(['id' => Auth::user()->city])->first();
        }


        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = ' Январь';
                break;
            case '02':
                $dateTitle = ' Февраль';
                break;
            case '03':
                $dateTitle = ' Март';
                break;
            case '04':
                $dateTitle = ' Апрель';
                break;
            case '05':
                $dateTitle = ' Май ';
                break;
            case '06':
                $dateTitle = ' Июнь ';
                break;
            case '07':
                $dateTitle = ' Июль ';
                break;
            case '08':
                $dateTitle = ' Август ';
                break;
            case '09':
                $dateTitle = ' Сентябрь ';
                break;
            case '10':
                $dateTitle = ' Октябрь ';
                break;
            case '11':
                $dateTitle = ' Ноябрь ';
                break;
            case '12':
                $dateTitle = ' Декабрь ';
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

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();
        $transactions = $city->transactionsPaginate()->get()->reverse();

//        dd($transactions);

        $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);


//        dd($transactions);

//        $loop=0;
//
//        foreach ($transactions as $transaction){
//            if($transaction->user){
//                echo $transaction->user->name;
//                $loop++;
//            }
//            else{
//                dd($transaction);
//                echo 'AAAAAAAAAAAA SUKAAAAAAAAAAAAAAA'.$loop;
//            }
//        }
//
//        dd($transactions);

        $states = TransactionState::all();
        return view('roles.director.transactions', compact('dateTitle', 'nextMonthLink', 'prevMonthLink', 'transactions', 'city', 'date', 'states'));
    }



    public function getMainOffice(Request $request)
    {
        $user = Auth::user();
        $city = City::where(['id' => 999])->first();


        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = ' Январь';
                break;
            case '02':
                $dateTitle = ' Февраль';
                break;
            case '03':
                $dateTitle = ' Март';
                break;
            case '04':
                $dateTitle = ' Апрель';
                break;
            case '05':
                $dateTitle = ' Май ';
                break;
            case '06':
                $dateTitle = ' Июнь ';
                break;
            case '07':
                $dateTitle = ' Июль ';
                break;
            case '08':
                $dateTitle = ' Август ';
                break;
            case '09':
                $dateTitle = ' Сентябрь ';
                break;
            case '10':
                $dateTitle = ' Октябрь ';
                break;
            case '11':
                $dateTitle = ' Ноябрь ';
                break;
            case '12':
                $dateTitle = ' Декабрь ';
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

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();
        $transactions = $city->transactionsPaginate()->get()->reverse();

//        dd($transactions);

        $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);


//        dd($transactions);

//        $loop=0;
//
//        foreach ($transactions as $transaction){
//            if($transaction->user){
//                echo $transaction->user->name;
//                $loop++;
//            }
//            else{
//                dd($transaction);
//                echo 'AAAAAAAAAAAA SUKAAAAAAAAAAAAAAA'.$loop;
//            }
//        }
//
//        dd($transactions);

        $states = TransactionState::all();
        return view('roles.director.transactions', compact('dateTitle', 'nextMonthLink', 'prevMonthLink', 'transactions', 'city', 'date', 'states'));
    }



    public function searchTransactions(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(['id' => Auth::user()->city])->first();
        }

        if($request->query('state')){
            $state=$request->query('state');
        }
        else{
            $state=0;
        }

        if($request->query('description')){
            $description=$request->query('description');
        }
        else{
            $description='';
        }

        if($request->query('type')){
            $type=$request->query('type');
        }
        else{
            $type='';
        }

        if($request->query('responsible')){
            $responsible=$request->query('responsible');
        }
        else{
            $responsible=0;
        }

        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = '';
        }

//        dd($transactions);

        $transactions=$city->getTransactionQuery($date,$description,$state,$type,$responsible);

        $allUsers=User::all();

        $states = TransactionState::all();
        return view('roles.director.transactions_search', compact('transactions', 'city', 'date', 'states','date','description','state','type','responsible','allUsers'));
    }

    public function doSearchTransaction(Request $request)
    {
        $data = $request->validate([
            "date" => '',
            "description" => '',
            "state" => '',
            "type" => '',
            "responsible" => '',
        ]);

        $temp=[];
        foreach ($data as $key=>$item){
            if($item!=null){
                $temp[$key]=$item;
            }
        }

        $query='?';
        $index=1;
        foreach ($temp as $key=>$item){
            if($index==count($temp)){
                $query=$query.$key.'='.$item;
            }
            else{
                $query=$query.$key.'='.$item.'&&';
                $index++;
            }
        }
        return redirect('/director/transactions/search/'.$query);
    }



    public function showTransactionDocs(Transaction $transaction)
    {
        $documents = explode('|', $transaction->documents);

        return view('roles.director.transaction', compact('transaction', 'documents'));
    }


    public function getDirectorDaysInMonthWithWeekdays($month, $year)
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
                'repairs_confirmed' => 0,
                'managers' => [],
                'workDay' => 0,
                'link' => $year . '-' . $month . '-' . ($day < 10 ? ('0' . $day) : ($day)),
            );
        }

        return $result;
    }

    public function getDirectorMonthLeads($year, $month, $city)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        return $leads = Repair::whereBetween('repair_date', [$startDate, $endDate])->get();

    }


    public function getDirectorWorkDays($year, $month, $director_id)
    {
        $startDate = Carbon::createFromDate(intval($year), intval($month), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $user = User::where(['id' => $director_id])->first();
        if ($user->hasRole('director')) {
            return DirectorWorkday::where([['director_id', '=', $director_id]])->whereBetween('created_at', [$startDate, $endDate])->get();
        } else {
            return EmployeerWorkDay::where([['user_id', '=', $director_id]])->whereBetween('created_at', [$startDate, $endDate])->get();
        }
    }


    public function directorCard(User $director, Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        if (!$director->id) {
            $director = Auth::user();
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

        $city = City::where(["id" => $director->city])->first();

        $days = $this->getDirectorDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);


        $monthLeads = Repair::whereBetween('repair_date',[Carbon::createFromDate($date)->startOfMonth()->toDateString(),Carbon::createFromDate($date)->endOfMonth()->toDateString()])->get();


        $suka=[];

        foreach ($monthLeads as $monthLead){
            if($city->name==$monthLead->lead->city){
                array_push($suka,$monthLead);
            }
        }


        $monthLeads=$suka;


        foreach ($monthLeads as $lead) {
            $day = intval(preg_split("/[^1234567890]/", $lead['repair_date'])[2]);
            if($lead->status=='completed'){
                $days[$day - 1]['repairs_confirmed'] += $lead->check;
            }
//            echo $lead->check;
//            echo array_search($lead->getManagerId->id, $days[$day - 1]['managers']);
            if (in_array($lead->lead->getManagerId->id, $days[$day - 1]['managers']) == false) {
                array_push($days[$day - 1]['managers'], $lead->lead->getManagerId->id);
//                echo $lead->getManagerId->id;
//                echo '------------';
            }
        }

//        dd($monthLeads);

        $monthWorkDays = $this->getDirectorWorkDays($dateTemp[0], $dateTemp[1], $director->id);


        foreach ($monthWorkDays as $workDay) {
            $day = intval(preg_split("/[^1234567890]/", $workDay['created_at'])[2]);
            $days[$day - 1]['workDay'] = $workDay->id;
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
        $weekends = 0;

        foreach ($days as $day) {
            if ($day['workDay'] != 0) {
                $totalWorkDays++;
            }
            if ($day['weekDay'] == 'вс') {
                $weekends++;
            }
            $totalConfirmed += $day['repairs_confirmed'];
        }

//        dd($days);

        $documents = explode('|', $director->documents);

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $bonuses = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $director->id, "type" => "plus"])->get();
        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $director->id, "type" => "minus"])->get();


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

        $employers = User::where(["city" => $city->id])->get();
        $temp = array();
        foreach ($employers as $employer) {
            if (!$employer->hasRole('director') && !$employer->hasRole('operator')) {
                array_push($temp, $employer);
            }
        }
        $employers = $temp;

        $totalProductsPercent=0.09;

        if ($totalConfirmed < 1000000) {
            $totalSalary = round((50000.0 / (count($days) - $weekends)) * $totalWorkDays);
        } elseif ($totalConfirmed >= 1000000 && $totalConfirmed < 2000000) {
            $totalSalary = round($totalConfirmed * 0.09);
            $totalProductsPercent=0.09;
        } else  {
            $totalSalary = round($totalConfirmed * 0.10);
            $totalProductsPercent=0.1;
        }

        $deductions = BonusManager::whereBetween('created_at', [$startDate, $endDate])->where(["user_id" => $director->id, "type" => 'minus'])->get();
        $totalDeduction = 0;
        foreach ($deductions as $deduction) {
            $totalDeduction += $deduction->amount;
        }

        $lowMargeChecksDeduction=0;

        $lowMargeChecks=Lead::whereBetween('created_at', [$startDate, $endDate])->where([["city","=",$director->city()->name],['salary_debuff','!=',false]])->get();


        foreach ($lowMargeChecks as $check){
            $lowMargeChecksDeduction+=$check->repair->check;
        }

        $lowMargeChecksDeduction=$lowMargeChecksDeduction*$totalProductsPercent;



//        dd($totalSalary);

        return view('cards.director', compact('date', 'dateTitle', 'formattedDate', 'city', 'director', 'days', 'nextMonthLink', 'prevMonthLink', 'totalWorkDays', 'totalConfirmed', 'documents', 'documents', 'weekends', 'bonuses', 'deductions', 'totalDeduction', 'totalBonus', 'totalSalary','lowMargeChecks','lowMargeChecksDeduction'));
    }

    public function addWorkDay(User $director, Request $request)
    {
        $date = $request['date'];
        if ($director->hasRole('director')) {
            $workDay = new DirectorWorkday(["director_id" => $director->id]);
        } else {
            $workDay = new EmployeerWorkDay(["user_id" => $director->id]);
        }

        $workDay->created_at = $date . ' 08:00:00';
        $workDay->updated_at = $date . ' 08:00:00';
        $workDay->save();
        return redirect()->back();
    }

    public function removeWorkDay(User $director, Request $request)
    {
        $date = $request['date'];
        if ($director->hasRole('director')) {
            $workDay = DirectorWorkday::whereDate('created_at', $date)->where(["director_id" => $director->id]);
        } else {
            $workDay = EmployeerWorkDay::whereDate('created_at', $date)->where(["user_id" => $director->id]);
        }
        $workDay->delete();
        return redirect()->back();
    }


    public function avanceView(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        $director = Auth::user();

        if ($director->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = $director->city();
        }

//        dd($director->city());
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
        $employers = User::where(["city" => $city->id])->get();
        $managers = array();
        $directors = array();
        $operators = array();
        $masters = array();
        $coordinators = array();
        foreach ($employers as $employer) {
            if ($employer->hasRole('manager')) {
                array_push($managers, $employer);
            } elseif ($employer->hasRole('director')) {
                array_push($directors, $employer);
            } elseif ($employer->hasRole('operator')) {
                array_push($operators, $employer);
            } elseif ($employer->hasRole('master')) {
                array_push($masters, $employer);
            } elseif ($employer->hasRole('coordinator')) {
                array_push($coordinators, $employer);
            }
        }

        return view('roles.director.avance', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'directors', 'masters', 'managers', 'operators', 'coordinators', 'date'));
    }


    public function avanceOperatorView(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        $director = Auth::user();

        if ($director->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = $director->city();
        }

//        dd($director->city());
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
        $employers = User::where(["city" => $city->id])->get();
        $managers = array();
        $directors = array();
        $operators = array();
        $masters = array();
        $coordinators = array();
        foreach ($employers as $employer) {
            if ($employer->hasRole('manager')) {
                array_push($managers, $employer);
            } elseif ($employer->hasRole('director')) {
                array_push($directors, $employer);
            } elseif ($employer->hasRole('operator')) {
                array_push($operators, $employer);
            } elseif ($employer->hasRole('master')) {
                array_push($masters, $employer);
            } elseif ($employer->hasRole('coordinator')) {
                array_push($coordinators, $employer);
            }
        }

        return view('roles.director.avance_operator', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'directors', 'masters', 'managers', 'operators', 'coordinators', 'date'));
    }



    public function avanceMonthView(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        $director = Auth::user();

        if ($director->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = $director->city();
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
        $employers = User::where(["city" => $city->id])->get();
        $managers = array();
        $directors = array();
        $operators = array();
        $masters = array();
        $coordinators = array();
        foreach ($employers as $employer) {
            if ($employer->hasRole('manager')) {
                array_push($managers, $employer);
            } elseif ($employer->hasRole('director')) {
                array_push($directors, $employer);
            } elseif ($employer->hasRole('operator')) {
                array_push($operators, $employer);
            } elseif ($employer->hasRole('master')) {
                array_push($masters, $employer);
            } elseif ($employer->hasRole('coordinator')) {
                array_push($coordinators, $employer);
            }
        }

        return view('roles.director.avancemonth', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'directors', 'masters', 'managers', 'operators', 'coordinators', 'date'));
    }


    public function payAvance(Request $request)
    {
        $data = $request->all();
        $data = array_slice($data, 2, count($data));
        $values = array();
        $users = array();
        $i = 0;
        foreach ($data as $item) {
            if ($i % 2 == 0) {
                array_push($values, $item);
            } else {
                array_push($users, $item);
            }
            $i++;
        }

        $counter = 0;

        foreach ($users as $user) {
            $recepient = User::where(["id" => $user])->first();
            $recepient->addSalary($values[$counter]);
            $counter++;
        }


        return redirect()->back();
    }


    public function salaryView(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        $director = Auth::user();

        if ($director->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = $director->city();
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
        $employers = User::where(["city" => $city->id])->get();
        $managers = array();
        $directors = array();
        $operators = array();
        $masters = array();
        $coordinators = array();
        foreach ($employers as $employer) {
            if ($employer->hasRole('manager')) {
                array_push($managers, $employer);
            } elseif ($employer->hasRole('director')) {
                array_push($directors, $employer);
            } elseif ($employer->hasRole('operator')) {
                array_push($operators, $employer);
            } elseif ($employer->hasRole('master')) {
                array_push($masters, $employer);
            } elseif ($employer->hasRole('coordinator')) {
                array_push($coordinators, $employer);
            }
        }

        return view('roles.director.salary', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'directors', 'masters', 'managers', 'operators', 'coordinators', 'date'));
    }



    public function operatorSalaryView(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }

        $director = Auth::user();

        if ($director->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = $director->city();
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
        $employers = User::where(["city" => $city->id])->get();
        $managers = array();
        $directors = array();
        $operators = array();
        $masters = array();
        $coordinators = array();
        foreach ($employers as $employer) {
            if ($employer->hasRole('manager')) {
                array_push($managers, $employer);
            } elseif ($employer->hasRole('director')) {
                array_push($directors, $employer);
            } elseif ($employer->hasRole('operator')) {
                array_push($operators, $employer);
            } elseif ($employer->hasRole('master')) {
                array_push($masters, $employer);
            } elseif ($employer->hasRole('coordinator')) {
                array_push($coordinators, $employer);
            }
        }

        return view('roles.director.salary_operator', compact('prevMonthLink', 'nextMonthLink', 'formattedDate', 'dateTitle', 'directors', 'masters', 'managers', 'operators', 'coordinators', 'date'));
    }


    public function paySalary(User $user, Request $request)
    {
        $date = $request->all();
        $date = $date['data'];


        $payedSalary = $user->payedSalary($date);

        $salary = $user->salary($date) - $payedSalary;

        $user->addSalary($salary);

        return redirect()->back();
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
                "productsSelled" => 0,
                "productsConfirmed" => 0,

            );
        }

        return $result;
    }


    public function getSellsView(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(['id' => Auth::user()->city])->first();
        }


        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = ' Январь';
                break;
            case '02':
                $dateTitle = ' Февраль';
                break;
            case '03':
                $dateTitle = ' Март';
                break;
            case '04':
                $dateTitle = ' Апрель';
                break;
            case '05':
                $dateTitle = ' Май ';
                break;
            case '06':
                $dateTitle = ' Июнь ';
                break;
            case '07':
                $dateTitle = ' Июль ';
                break;
            case '08':
                $dateTitle = ' Август ';
                break;
            case '09':
                $dateTitle = ' Сентябрь ';
                break;
            case '10':
                $dateTitle = ' Октябрь ';
                break;
            case '11':
                $dateTitle = ' Ноябрь ';
                break;
            case '12':
                $dateTitle = ' Декабрь ';
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

        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]);

        $users = User::where(["city" => $city->id])->withTrashed()->get();
        $managers = array();
        foreach ($users as $user) {
            if ($user->hasRole('manager')) {
                array_push($managers, $user);
            }
        }

        $managersCalendar = array();

        foreach ($managers as $manager) {
            array_push($managersCalendar, [$manager, $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0]), ['productsSelled' => 0, 'productsConfirmed' => 0]]);
        }

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $leads = Lead::whereBetween("created_at", [$startDate, $endDate])->where([["city", '=', $city->name], ["manager_id", "!=", null]])->get();

        $logs = array();
        foreach ($leads as $lead) {
            $manager = $lead->getManagerId->id;
            $neededObject = array_filter(
                $managersCalendar,
                function ($e) use (&$manager) {
                    return $e[0]->id == $manager;
                }
            );
            $neededObject = array_key_first($neededObject);
            $neededDay = array_filter(
                $managersCalendar[$neededObject][1],
                function ($e) use (&$lead) {
                    return $e['date'] == $lead->created_at->toDateString();
                }
            );
//            dd(array_key_first($neededDay));
            $neededDay = array_key_first($neededDay);
            $managersCalendar[$neededObject][1][$neededDay]['productsSelled'] += $lead->check;
            $managersCalendar[$neededObject][1][$neededDay]['productsConfirmed'] += $lead->repair ? $lead->repair->check : 0;
            $managersCalendar[$neededObject][2]['productsSelled'] += intval($lead->check);
//            if($manager==34){
//                array_push($logs,[$lead,$lead->check]);
//            }
            $managersCalendar[$neededObject][2]['productsConfirmed'] += $lead->repair ? intval($lead->repair->check) : 0;
        }

//        dd($logs);


        return view('roles.director.statistic.sells', compact('dateTitle', 'nextMonthLink', 'prevMonthLink', 'city', 'date', 'days', 'managersCalendar'));
    }


//    function sortу($a,$b){
//        return $a[1]['productsConfirmed']-$b[1]['productsConfirmed'];
//    }

    public function posyGramm(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin) {
            $city = Session::get('city');
        } else {
            $city = City::where(['id' => Auth::user()->city])->first();
        }


        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = ' Январь';
                break;
            case '02':
                $dateTitle = ' Февраль';
                break;
            case '03':
                $dateTitle = ' Март';
                break;
            case '04':
                $dateTitle = ' Апрель';
                break;
            case '05':
                $dateTitle = ' Май ';
                break;
            case '06':
                $dateTitle = ' Июнь ';
                break;
            case '07':
                $dateTitle = ' Июль ';
                break;
            case '08':
                $dateTitle = ' Август ';
                break;
            case '09':
                $dateTitle = ' Сентябрь ';
                break;
            case '10':
                $dateTitle = ' Октябрь ';
                break;
            case '11':
                $dateTitle = ' Ноябрь ';
                break;
            case '12':
                $dateTitle = ' Декабрь ';
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


        $users = User::where(["city" => $city->id])->withTrashed()->get();
        $managers = array();
        foreach ($users as $user) {
            if ($user->hasRole('manager')) {
                array_push($managers, $user);
            }
        }

        $managersCalendar = array();

        foreach ($managers as $manager) {
            array_push($managersCalendar, [$manager, 'productsConfirmed' => 0, 'productsSelled' => 0]);
        }

        $startDate = Carbon::createFromDate(intval($dateTemp[0]), intval($dateTemp[1]), 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dateTemp[0], $dateTemp[1], 1)->endOfMonth();

        $leads = Lead::whereBetween("created_at", [$startDate, $endDate])->where([["city", '=', $city->name], ["manager_id", "!=", null]])->get();

        $logs = array();
        $totalSelled=0;
        $totalConfirmed=0;
        foreach ($leads as $lead) {
            $manager = $lead->getManagerId->id;
            $neededObject = array_filter(
                $managersCalendar,
                function ($e) use (&$manager) {
                    return $e[0]->id == $manager;
                }
            );
            $neededObject = array_key_first($neededObject);
            $managersCalendar[$neededObject]['productsSelled'] += intval($lead->check);
            $totalSelled+= intval($lead->check);

            $managersCalendar[$neededObject]['productsConfirmed'] += $lead->repair&&$lead->repair->status=='completed' ? intval($lead->repair->check) : 0;
            $totalConfirmed+= $lead->repair&&$lead->repair->status=='completed' ? intval($lead->repair->check) : 0;
        }
        $managersCalendar = collect($managersCalendar);

        $managersCalendar = $managersCalendar->sortByDesc('productsConfirmed');



//        dd($managersCalendar);

        return view('roles.director.statistic.posygramm', compact('dateTitle', 'nextMonthLink', 'prevMonthLink', 'city', 'date', 'managersCalendar','totalSelled','totalConfirmed'));
    }


    public function posyCitites(Request $request)
    {
        $user = Auth::user();

        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $dateTemp = preg_split("/[^1234567890]/", $date);

        $dateTitle = '';
        switch ($dateTemp[1]) {
            case '01':
                $dateTitle = ' Январь';
                break;
            case '02':
                $dateTitle = ' Февраль';
                break;
            case '03':
                $dateTitle = ' Март';
                break;
            case '04':
                $dateTitle = ' Апрель';
                break;
            case '05':
                $dateTitle = ' Май ';
                break;
            case '06':
                $dateTitle = ' Июнь ';
                break;
            case '07':
                $dateTitle = ' Июль ';
                break;
            case '08':
                $dateTitle = ' Август ';
                break;
            case '09':
                $dateTitle = ' Сентябрь ';
                break;
            case '10':
                $dateTitle = ' Октябрь ';
                break;
            case '11':
                $dateTitle = ' Ноябрь ';
                break;
            case '12':
                $dateTitle = ' Декабрь ';
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


        $cities=City::where([["id","!=",999]])->get();

        $citiesCalendar = array();

        foreach ($cities as $city) {
            array_push($citiesCalendar, [$city, 'productsConfirmed' => 0, 'productsSelled' => 0]);
        }

        $startDate = Carbon::createFromDate($date)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($date)->endOfMonth()->toDateString();

//        dd($startDate,$endDate);

        $leads = Lead::whereBetween("meeting_date", [$startDate, $endDate])->where([["status","!=","declined"]])->get();
//        dd($leads);
        $logs = array();
        $totalSelled=0;
        $totalConfirmed=0;

        foreach ($leads as $lead) {
            $cityTemp = City::where(["name"=>$lead->city])->first();
            $neededObject = array_filter(
                $citiesCalendar,
                function ($e) use (&$cityTemp) {
                    return $e[0]->id == $cityTemp->id;
                }
            );
            $neededObject = array_key_first($neededObject);
            $citiesCalendar[$neededObject]['productsSelled'] += intval($lead->check);
            $totalSelled+= intval($lead->check);
            $citiesCalendar[$neededObject]['productsConfirmed'] += $lead->repair&&$lead->repair->status=='completed' ? intval($lead->repair->check) : 0;
            $totalConfirmed+= $lead->repair&&$lead->repair->status=='completed' ? intval($lead->repair->check) : 0;
        }
        $citiesCalendar = collect($citiesCalendar);

        $citiesCalendar = $citiesCalendar->sortByDesc('productsConfirmed');

//        dd($managersCalendar);

        return view('roles.director.statistic.posygramm_cities', compact('dateTitle', 'nextMonthLink', 'prevMonthLink', 'cities', 'date', 'citiesCalendar','totalConfirmed','totalSelled'));
    }



}
