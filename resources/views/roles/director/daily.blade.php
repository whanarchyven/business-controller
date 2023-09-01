@extends('layouts.app')
@section('title')
    {{'- Дневная сводка'}}
@endsection
@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/daily?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/daily?date={{$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>

        <div class="">

            <div class="bd-cyan-500">
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Дата</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col"></th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col">Менеджер</th>
                        <th class="fw-bold text-left" scope="col">Сумма</th>
                        <th class="fw-bold text-left" scope="col">Принять</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <th class="bg-{{$lead->documents?'completed':''}} p-3 fw-bold text-left" scope="col">
                                <p class="mb-0">{{$lead->created_at}}<br/></p>
                                {{$lead->city}}<br/>
                            </th>
                            <th class="p-3 fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$lead->client_fullname}}</p>
                                <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$lead->address}}</p>
                            </th>
                            <th class="p-3 fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>Телефон: </strong> <br/>{{$lead->phone}}</p>
                                <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$lead->comment}}</p>
                            </th>
                            <th class="p-3 fw-bold text-left" scope="col">{{$lead->jobType->name}}</th>
                            <th class="p-3 fw-bold text-left" scope="col">
                                <div class="d-flex flex-column">
                                    <div class="d-flex gap-5 flex-row">
                                        <p>Принял <br/> {{$lead->accepted}}</p>
                                        <p>Вошёл <br/> {{$lead->entered}}</p>
                                        <p>Вышел <br/> {{$lead->exited}}</p>
                                    </div>
                                    @if($lead->manager_id)
                                        <p class="fw-bold">{{$lead->getManagerId->name}}</p>
                                    @else
                                        <p class="fw-bold">Не назначено</p>
                                    @endif
                                </div>
                            </th>
                            <th class="p-3 fw-bold text-left" scope="col">
                                @if($lead->check)
                                    <p class="fw-bold">{{$lead->check}}</p>
                                @else
                                    <p class="fw-bold">0</p>
                                @endif
                            </th>
                            <th class="p-3 fw-bold text-left" scope="col">
                                @if(!$lead->documents)
                                    <button onclick="window.location='{{route('director.leads.accept',$lead->id)}}'"
                                            class="bg-success text-white rounded-2 w-100 p-2">
                                        Принять
                                    </button>
                                @endif
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
