@extends('layouts.app')

@section('title')
    {{'- Карточка'}}
@endsection

@section('content')
    <div class="container">
        <p class="fs-2 fw-bold">{{$user->name}}</p>
        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/card?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/card?date={{$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>

        <div class="">
            <table class="table table-bordered table-sm table-secondary ">
                <thead>
                <tr>
                    <th class="fw-normal text-center" scope="col">День недели</th>
                    @foreach($days as $day)
                        @if($day['weekDay']=='вс')
                            <th class="fw-normal text-center text-danger" scope="col">{{$day['weekDay']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col">{{$day['weekDay']}}</th>
                        @endif
                    @endforeach
                    <th class="fw-normal text-center" scope="col">Итого</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="fw-normal text-center" scope="row">Число</th>
                    @foreach($days as $day)
                        <th class="text-center fw-light" scope="col">{{$day['day']}}
                        </th>
                    @endforeach
                    <th class="fw-normal text-center" scope="row">Итого</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Заявок</th>
                    @foreach($days as $day)
                        @if($day['leads']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['leads']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalLeads}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Успешных</th>
                    @foreach($days as $day)
                        @if($day['leads']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['successful']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalSuccessful}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Отказов</th>
                    @foreach($days as $day)
                        @if($day['leads']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['declined']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalDeclined}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Раб. день</th>
                    @foreach($days as $day)
                        @if($day['leads']!=0)
                            <th class="fw-bold text-center" scope="col">✓</th>
                        @else
                            <th class="fw-normal text-center" scope="col">✕</th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalWorkDays}}/{{count($days)}}</th>
                </tr>

                </tbody>

            </table>

            <div class="bd-cyan-500">
                <table class="table table-bordered table-sm table-secondary ">
                    <thead class="">
                    <tr>
                        <th class="fw-bold text-left" scope="col">Премия</th>
                        <th class="fw-bold text-left" scope="col">За заявки</th>
                        <th class="fw-bold text-left" scope="col">За выходы</th>
                        <th class="fw-bold text-left" scope="col">Рабочих дней</th>
                        <th class="fw-bold text-left" scope="col">Общая сумма удержаний</th>
                        <th class="fw-bold text-left" scope="col">Сумма к выдаче</th>

                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th class="fw-normal text-left" scope="col">
                            0
                        </th>
                        <th class="fw-normal text-left" scope="col">
                            {{$totalSuccessful*150}}
                        </th>
                        <th class="fw-normal text-left" scope="col">
                            {{$totalWorkDays*200}}
                        </th>
                        <th class="fw-normal text-left" scope="col">
                            {{$totalWorkDays}}
                        </th>
                        <th class="fw-normal text-left" scope="col">
                            0
                        </th>
                        <th class="fw-normal text-left" scope="col">
                            {{$user->getSalary()->salary}}
                        </th>

                    </tr>
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
