@extends('layouts.app')

@section('title')
    {{'- Оформленные заявки'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/leads?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/leads?date={{$nextMonthLink}}">Следующий
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
                        <th class="text-primary text-center" scope="col"><a
                                href="/leads/?date={{$day['link']}}">{{$day['day']}}</a>
                        </th>
                    @endforeach
                    <th class="fw-normal text-center" scope="row">Итого</th>
                </tr>
                <tr>
                    <th scope="row text-center">Заявки</th>
                    @foreach($days as $day)
                        @if($day['leads']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['leads']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalLeads}}</th>
                </tr>

                </tbody>

            </table>

            <div class="bd-cyan-500">
                <p class="fs-3 text-indigo">Заявки {{$formattedDate}}</p>
{{--                <div class="flex gap-3 mb-4">--}}
{{--                    <button class="bg-not-managed btn-outline-info border-0 p-2 text-white rounded-3">Не назначено--}}
{{--                    </button>--}}
{{--                    <button class="bg-managed btn-outline-info border-0 p-2 text-white rounded-3">Назначено</button>--}}
{{--                    <button class="bg-in-work btn-outline-info border-0 p-2 text-white rounded-3">В работе</button>--}}
{{--                    <button class="bg-accepted btn-outline-info border-0 p-2 text-white rounded-3">Принято</button>--}}
{{--                    <button class="bg-completed btn-outline-info border-0 p-2 text-white rounded-3">Выполнено</button>--}}
{{--                </div>--}}
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Дата</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col"></th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col">Менеджер</th>
                        <th class="fw-bold text-left" scope="col">Сумма</th>
                        <th class="fw-bold text-left" scope="col">Редактировать</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <th class="fw-bold bg-{{$lead->status}} text-left" scope="col">
                                <p class="mb-0">Заявка от {{$lead->created_at}}<br/></p>
                                {{$lead->city}}<br/>
                                c {{preg_split("/[^1234567890]/", $lead->time_period)[0]}}
                                до {{preg_split("/[^1234567890]/", $lead->time_period)[1]}}
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$lead->client_fullname}}</p>
                                <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$lead->address}}</p>
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>Телефон: </strong> <br/>{{$lead->phone}}</p>
                                <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$lead->comment}}</p>
                            </th>
                            <th class="fw-normal text-left" scope="col">{{$lead->jobType->name}}</th>
                            <th class="fw-bold text-left" scope="col">
                                @if($lead->manager_id)
                                    <p class="fw-normal">{{$lead->getManagerId->name}}</p>
                                @else
                                    <p class="fw-normal">Не назначено</p>
                                @endif
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                @if($lead->check&&!\Illuminate\Support\Facades\Auth::user()->hasRole('operator'))
                                    <p class="fw-normal">{{$lead->check}}</p>
                                @else
                                    <p class="fw-normal"></p>
                                @endif
                                    @if($lead->avance&&!\Illuminate\Support\Facades\Auth::user()->hasRole('operator'))
                                        <p class="fw-normal">({{$lead->avance}})</p>
                                    @else
                                        <p class="fw-normal"></p>
                                    @endif
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <button onclick="window.location='{{route('leads.edit',$lead->id)}}'"
                                        class="bg-primary text-white rounded-2 w-100 p-2">
                                    Редатировать
                                </button>
                                <p class="fw-normal">{{$lead->getOperatorId->name}}</p>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
