<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Gsm;
use App\Models\Lead;
use App\Models\TransactionState;
use App\Models\User;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;

class GsmController extends Controller
{

    public function getFirstMonday($month, $year)
    {
        $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
        return Carbon::createFromDate($monday)->toDateString();

    }

    public function getDaysInMonthWithWeekdays($month, $year,$day_start)
    {
        $weekDays = array(
            1 => 'пн',
            2 => 'вт',
            3 => 'ср',
            4 => 'чт',
            5 => 'пт',
            6 => 'сб',
            7 => 'вс'
        );


        $result=array(['day' => Carbon::createFromDate($day_start)->day,
            'date' => Carbon::createFromDate($day_start)->toDateTimeString(),
            'weekDay' => $weekDays[Carbon::createFromDate($day_start)->weekday()],
            "gsm"=>0,
            "gsm_id"=>'',
            "gsm_is_payed"=>false,
            "total_gsm"=>0]);

        for ($i=1;$i<6;$i++){
            array_push($result,['day' => Carbon::createFromDate($day_start)->addDay($i)->day,
                'date' => Carbon::createFromDate($day_start)->addDay($i)->toDateTimeString(),
                'weekDay' => $weekDays[Carbon::createFromDate($day_start)->addDay($i)->weekday()],
                "gsm"=>0,
                "gsm_id"=>'',
                "gsm_is_payed"=>false,
                "total_gsm"=>0]);
        }

//        dd($result);

        return $result;
    }

    public function indexGsm(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = $user->city;
        if ($user->isAdmin){
            $city=Session::get('city');
        }
        else{

            $city = City::where(["id" => $city])->first();
        }

//        dd($city);

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

        if($request->query('day')){
            $day_start=$request->query('day');
        }
        else{
            $day_start=$this->getFirstMonday($dateTemp[1],$dateTemp[0]);
        }

        $nextMonday=Carbon::createFromDate(date( 'Y-m-d', strtotime( 'next monday',strtotime($date) ) ))->toDateString();
        $prevMonday=Carbon::createFromDate(date( 'Y-m-d', strtotime( 'previous monday',strtotime($date) ) ))->toDateString();

//        dd($nextMonday,$prevMonday);

        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0],$day_start);



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

        $managers=User::where(["city"=>$city->id])->get();

        $managers_gsm=[];

        foreach ($managers as $manager){
            if($manager->hasRole('manager')){
                $manager_days=$this->getDaysInMonthWithWeekdays($dateTemp[1],$dateTemp[0],$day_start);
                $manager_gsm=Gsm::where(["user_id"=>$manager->id])->get();
                $total_gsm=0;
                if(count($manager_gsm)>0){
                    foreach ($manager_gsm as $mg){
                        $dt=$mg->created_at;
//                        dd($manager_days);
//                        dd($dt);
                        $found_key = array_search($dt, array_column($manager_days, 'date'));
                        $found_key===false?$found_key='suka':$found_key=$found_key;
//                        dd($found_key);
//                        if($dt==6){
//                            dd($manager_days,$dt,$found_key);
//                        }
                        if($found_key!='suka'){
                            $manager_days[$found_key]["gsm"]=$mg->amount;
                            $manager_days[$found_key]["gsm_id"]=$mg->id;
                            $manager_days[$found_key]["total_gsm"]+=$mg->amount;
                            $manager_days[$found_key]["gsm_is_payed"]=$mg->is_payed;
                            $total_gsm+=$mg->amount;
                        }
//                        dd($manager_days,$dt,$manager_gsm,$mg,$found_key);
                    }
                }
                array_push($managers_gsm,[$manager,$manager_days,$total_gsm]);
            }
        }

//        dd($managers_gsm);
//        dd($managers_gsm);

        return view('gsm.show', compact('date', 'dateTitle', 'formattedDate', 'city', 'days', 'nextMonthLink', 'prevMonthLink','managers_gsm','nextMonday','prevMonday','date'));
    }

    public function indexGsmMaster(Request $request)
    {
        if ($request->query('date')) {
            $date = $request->query('date');
        } else {
            $date = Carbon::now()->toDateString();
        }
        $user = Auth::user();
        $city = $user->city;
        if ($user->isAdmin){
            $city=Session::get('city');
        }
        else{

            $city = City::where(["id" => $city])->first();
        }

//        dd($city);

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

        if($request->query('day')){
            $day_start=$request->query('day');
        }
        else{
            $day_start=$this->getFirstMonday($dateTemp[1],$dateTemp[0]);
        }

        $nextMonday=Carbon::createFromDate(date( 'Y-m-d', strtotime( 'next monday',strtotime($date) ) ))->toDateString();
        $prevMonday=Carbon::createFromDate(date( 'Y-m-d', strtotime( 'previous monday',strtotime($date) ) ))->toDateString();

//        dd($nextMonday,$prevMonday);

        $days = $this->getDaysInMonthWithWeekdays($dateTemp[1], $dateTemp[0],$day_start);



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

        $managers=User::where(["city"=>$city->id])->get();

        $managers_gsm=[];

        foreach ($managers as $manager){
            if($manager->hasRole('master')){
                $manager_days=$this->getDaysInMonthWithWeekdays($dateTemp[1],$dateTemp[0],$day_start);
                $manager_gsm=Gsm::where(["user_id"=>$manager->id])->get();
                $total_gsm=0;
                if(count($manager_gsm)>0){
                    foreach ($manager_gsm as $mg){
                        $dt=$mg->created_at;
//                        dd($manager_days);
//                        dd($dt);
                        $found_key = array_search($dt, array_column($manager_days, 'date'));
                        $found_key===false?$found_key='suka':$found_key=$found_key;
//                        dd($found_key);
//                        if($dt==6){
//                            dd($manager_days,$dt,$found_key);
//                        }
                        if($found_key!='suka'){
                            $manager_days[$found_key]["gsm"]=$mg->amount;
                            $manager_days[$found_key]["gsm_id"]=$mg->id;
                            $manager_days[$found_key]["total_gsm"]+=$mg->amount;
                            $manager_days[$found_key]["gsm_is_payed"]=$mg->is_payed;
                            $total_gsm+=$mg->amount;
                        }
//                        dd($manager_days,$dt,$manager_gsm,$mg,$found_key);
                    }
                }
                array_push($managers_gsm,[$manager,$manager_days,$total_gsm]);
            }
        }

//        dd($managers_gsm);
//        dd($managers_gsm);

        return view('gsm.show', compact('date', 'dateTitle', 'formattedDate', 'city', 'days', 'nextMonthLink', 'prevMonthLink','managers_gsm','nextMonday','prevMonday','date'));
    }


    public function createGsm(Request $request){
        $data=$request->all();
        $gsm=new Gsm(["created_at"=>$data['date'],"amount"=>$data['amount'],"city_id"=>$data['city'],"user_id"=>$data['manager']]);
        $gsm->save();
        $gsm->created_at=$data['date'];
        $gsm->save();
        return redirect()->back();
    }

    public function payGsm(Gsm $gsm, Request $request){
        $role=$request->get('role');
        if($role=='manager'){
            $state = TransactionState::getByCode('2.05.11.');
        }
        else{
            $state = TransactionState::getByCode('2.05.12.');
        }
        $manager=$gsm->user();
        if($role=='manager'){
            $desc='Выплата ГСМ менеджер '.$manager->name.' за '.Carbon::createFromDate($gsm->created_at)->toDateString();
        }
        else{
            $desc='Выплата ГСМ мастер '.$manager->name.' за '.Carbon::createFromDate($gsm->created_at)->toDateString();
        }
        $value=$gsm->amount;
        $responsible=Auth::user()->id;
        $city_id=$gsm->city_id;
        $documents='';
        $transaction = app(\App\Http\Controllers\TransactionController::class)->newExpense($state->id, $desc, $value, $responsible, $city_id, $documents);
        $gsm->is_payed=true;
        $gsm->save();
        return redirect()->back();
    }
}
