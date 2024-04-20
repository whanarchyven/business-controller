@extends('layouts.app')

@section('title')
    {{'- Транзакции'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.statistic.sells')}}?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.statistic.sells')}}?date={{$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>

        <div class="">
            <div class="bd-cyan-500">

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
                    <tr>
                        <th class="fw-normal table-light text-center" scope="col">Число</th>
                        @foreach($days as $day)
                            <th class="fw-normal table-light text-center" scope="col">{{$day['day']}}</th>
                        @endforeach
                        <th class="fw-bold table-light text-center" scope="col">Итого за месяц</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($managersCalendar as $manager)
                        <tr>
                            <th class="fw-normal table-light text-center" scope="col">{{$manager[0]->shortname()}}</th>
                            @foreach($manager[1] as $day)
                                <th class="fw-normal table-light text-center" scope="col">{{round($day['productsSelled']/1000,2)}}<br/>{{round($day['productsConfirmed']/1000,2)}}</th>
                            @endforeach
                            <th class="fw-bold table-light text-center" scope="col">{{round($manager[2]['productsSelled']/1000,2)}}<br/>{{round($manager[2]['productsConfirmed']/1000,2)}}</th>
                        </tr>
                    @endforeach
                    @foreach($totalCalendar as $manager)
                        <tr>
                            <th class="fw-bold table-light text-center" scope="col">Итого за день</th>
                            @foreach($manager[0] as $day)
                                <th class="fw-bold table-light text-center" scope="col">{{round($day['productsSelled']/1000,2)}}<br/>{{round($day['productsConfirmed']/1000,2)}}</th>
                            @endforeach
                        </tr>
                    @endforeach
                    

                    </tbody>

                </table>

            </div>

        </div>

    </div>
@endsection
