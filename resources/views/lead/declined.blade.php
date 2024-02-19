@extends('layouts.app')

@section('title')
    {{'- Отказанные заявки'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/leads/declined?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/leads/declined?date={{$nextMonthLink}}">Следующий
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
                                href="/leads/declined/?date={{$day['link']}}">{{$day['day']}}</a>
                        </th>
                    @endforeach
                    <th class="fw-normal text-center" scope="row">Итого</th>
                </tr>
                <tr>
                    <th scope="row text-center">Отклонённые</th>
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
                    <th scope="row text-center">Нулевые</th>
                    @foreach($days as $day)
                        @if($day['null']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['null']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalNull}}</th>
                </tr>

                </tbody>

            </table>

            <div class="bd-cyan-500">
                <p class="fs-3 text-indigo">Отклонённые {{$formattedDate}}</p>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Дата</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col"></th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col">Менеджер</th>
                        <th class="fw-bold text-left" scope="col">Вернуть в работу</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <th class="fw-bold bg-{{$lead->exited?'in-work':$lead->status}} text-left" scope="col">
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
                            <th class="fw-normal text-left" scope="col">{{$lead->note}}</th>
                            <th class="fw-bold text-left" scope="col">
                                @if($lead->manager_id)
                                    <p class="fw-normal">{{$lead->getManagerId->shortname()}}</p>
                                @else
                                    <p class="fw-normal">Не назначено</p>
                                @endif
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <button onclick="window.location='{{route('leads.edit',$lead->id)}}'"
                                        class="bg-warning text-black rounded-2 w-100 p-2">
                                    Вернуть в работу
                                </button>
                                <p class="fw-normal">{{$lead->getOperatorId->shortname()}}</p>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
