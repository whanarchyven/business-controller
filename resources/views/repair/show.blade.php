@extends('layouts.app')

@section('title')
    {{'- Таблица ремонтов'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/repairs?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/repairs?date={{$nextMonthLink}}">Следующий
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
                                href="/repairs/?date={{$day['link']}}">{{$day['day']}}</a>
                        </th>
                    @endforeach
                    <th class="fw-normal text-center" scope="row">Итого</th>
                </tr>
                <tr>
                    <th scope="row text-center">Ремонты</th>
                    @foreach($days as $day)
                        @if($day['repairs']!=0)
                            <th class="fw-normal text-center {{$day['repairs']==$day['completed']+$day['declined']+$day['refund']?'bg-completed':''}}"
                                scope="col">{{$day['repairs']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalRepairs}}</th>
                </tr>
                <tr>
                    <th scope="row text-center">Отказы</th>
                    @foreach($days as $day)
                        @if($day['declined']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['declined']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalDeclined}}</th>
                </tr>

{{--                <tr>--}}
{{--                    <th scope="row text-center">Выполнено</th>--}}
{{--                    @foreach($days as $day)--}}
{{--                        @if($day['completed']!=0)--}}
{{--                            <th class="fw-normal text-center" scope="col">{{$day['completed']}}</th>--}}
{{--                        @else--}}
{{--                            <th class="fw-normal text-center" scope="col"></th>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                    <th class="fw-normal  text-center" scope="row">{{$totalCompleted}}</th>--}}
{{--                </tr>--}}

                </tbody>

            </table>

            <div class="bd-cyan-500">
                <div class="d-flex mb-3 gap-3 align-items-center">
                    <p class="fs-3 m-0 text-indigo">Ремонты {{$formattedDate}}</p>
                </div>
                <div class="w-100 d-flex flex-row justify-content-between">
                    <div class="d-flex gap-3 mb-4">
{{--                        <button class="bg-in-work btn-outline-info border-0 p-2 text-white rounded-3">В работе</button>--}}
{{--                        <button class="bg-declined btn-outline-info border-0 p-2 text-white rounded-3">Отказанно--}}
{{--                        </button>--}}
{{--                        <button class="bg-completed btn-outline-info border-0 p-2 text-white rounded-3">Выполнено--}}
{{--                        </button>--}}
{{--                        <button class="bg-refund  p-2 text-white rounded-3">Возврат--}}
{{--                        </button>--}}
                    </div>
                    <p class="my-4 fw-bold fs-3">Суммарный чек: {{$totalCheck}} ({{$totalAvance}})</p>
                </div>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Статус</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col">Список работ</th>
                        <th class="fw-bold text-left" scope="col">Тип работ</th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col">Сумма</th>
                        <th class="fw-bold text-left" scope="col">Специалисты</th>
                        <th class="fw-bold text-left" scope="col">Редактировать</th>
                        <th class="fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($repairs as $repair)
                        <tr>
                            <th class="fw-bold bg-{{$repair->status}} text-left" scope="col">
                                <div class="d-flex flex-column
                                 gap-3">
                                    <p class="mb-0">Заявка от {{$repair->lead->created_at}}<br/></p>
                                    {{$repair->lead->city}}<br/>
                                    c {{preg_split("/[^1234567890]/", $repair->lead->time_period)[0]}}
                                    до {{preg_split("/[^1234567890]/", $repair->lead->time_period)[1]}}
                                    <p class="mb-0">{{$repair->contract_number?'Договор: #'.$repair->contract_number:''}}<br/></p>
                                    <form method="post" action="{{route('repairs.duplicate',$repair)}}">
                                        @csrf
                                        @method('post')
                                        <input type="submit" class="btn btn-primary mt-3" value="Дублировать">
                                    </form>
                                </div>

                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$repair->lead->client_fullname}}</p>
                                <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$repair->lead->address}}</p>
                                <p class="mb-0 fw-normal"><strong>Телефон: </strong> <br/>{{$repair->lead->phone}}</p>
                                <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$repair->lead->comment}}</p>
                            </th>

                            <th class="fw-normal text-left" scope="col">{{$repair->works}}</th>
                            <th class="fw-normal text-left" scope="col">{{$repair->lead->jobType->name}}</th>
                            <th class="fw-normal text-left" scope="col">{{$repair->lead->note}}</th>
                            <th class="fw-normal text-left" scope="col">{{$repair->lead->issued}}
                                /{{$repair->lead->avance}}
                            </th>
                            <th class="fw-bold  text-left" scope="col">
                                <div class="d-flex flex-column">
                                    @if($repair->master)
                                        <p class="fw-normal">Мастер: <br/><span class="fw-bold">{{$repair->master->shortname()}} {{$repair->master_boost?'★':''}}</span> </p>
                                        <p class="fw-normal">Менеджер:<br/> <span class="fw-bold">{{$repair->lead->getManagerId->shortname()}}</span></p>
                                    @else
                                        <p class="fw-normal">Мастер: <br/><span class="fw-bold">Не назначено</span></p>
                                        <p class="fw-normal">Менеджер:<br/> <span class="fw-bold">{{$repair->lead->getManagerId->shortname()}}</span></p>
                                    @endif
                                </div>
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                <div class="d-flex flex-column gap-1">
                                    <button onclick="window.location='{{route('repairs.edit',$repair)}}'"
                                            class="btn btn-warning text-white rounded-2 w-100 p-2">
                                        Редатировать
                                    </button>
                                    <form class="d-flex flex-column gap-1" method="post"
                                          action="{{route('repairs.update',$repair)}}">
                                        @csrf
                                        @method('patch')
                                        <input required type="date" class="form-control"
                                               id='repair_date' name="repair_date" list="repair_date">
                                        <input type="hidden" class="form-control"
                                               id='status' value="{{$repair->status}}" name="status" list="status">
                                        <input type="submit" class="btn btn-primary w-100" value="Перенос">
                                    </form>
                                </div>
                            </th>
                            <th class="fw-bold {{$repair->lead->marge()>=35?'bg-completed':'bg-declined'}} text-left" scope="col">
                                <p class="m-0 fw-normal">Сумма ремонта: <span class="fw-bold">{{$repair->lead->issued}}</span></p>
                                <p class="m-0 fw-normal">Стоимость материала: <span class="fw-bold">{{$repair->materialPrice()}}</span></p>
                                <p class="m-0 fw-normal">ЗП мастера: <span class="fw-bold">{{$repair->master?$repair->lead->issued*($repair->master_boost?0.15:0.1):0}}</span></p>
                                <p class="m-0 fw-normal">ЗП менеджера: <span class="fw-bold">{{$repair->lead->issued*0.2}}</span></p>
                                <p class="m-0 fw-normal">Прочие затраты: <span class="fw-bold">{{$repair->lead->issued*0.2}}</span></p>
                                <p class="m-0 fw-normal">
                                    Прибыль: <span class="fw-bold">{{$repair->lead->profit()}} <br/>
                                    - <br/>
                                    {{$repair->lead->marge()}}%</span></p>

                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
