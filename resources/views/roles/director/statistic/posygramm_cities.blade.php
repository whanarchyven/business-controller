@extends('layouts.app')

@section('title')
    {{'- Позиграмма'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.statistic.posygramm.cities')}}?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.statistic.posygramm.cities')}}?date={{$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>

        <div class="">
            <div class="bd-cyan-500">

                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-normal p-2 text-center" scope="col">Место</th>
                        <th class="fw-normal p-2 text-left" scope="col">Город</th>
                        <th class="fw-normal p-2 text-left" scope="col">ТО Продано</th>
                        <th class="fw-normal p-2 text-left" scope="col">ТО Зачет</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($citiesCalendar as $city)
                        <tr>
                            <th class="fw-normal p-2 table-light text-center" scope="col">{{$loop->iteration}}</th>
                            <th class="fw-normal p-2 table-light text-left" scope="col">{{$city[0]->name}}</th>
                            <th class="fw-bold p-2 table-light text-left" scope="col">{{$city['productsSelled']}}</th>
                            <th class="fw-bold p-2 table-light text-left" scope="col">{{$city['productsConfirmed']}}</th>
                        </tr>
                    @endforeach
                    <tr>
                        <th class="fw-normal p-2 text-center" scope="col"></th>
                        <th class="fw-bold p-2 text-left" scope="col">Итого</th>
                        <th class="fw-bold p-2 text-left" scope="col">{{$totalSelled}}</th>
                        <th class="fw-bold p-2 text-left" scope="col">{{$totalConfirmed}}</th>
                    </tr>
                    </tbody>

                </table>

            </div>

        </div>

    </div>
@endsection
