@extends('layouts.app')
@section('title')
    {{'- Карточка'}}
@endsection
@section('content')
    <div class="container">
        <div class="d-flex align-items-center gap-3 my-3">
            <p class="fw-bold mb-0 fs-3">{{$manager->name}}</p>
            @role('coordinator')
            <button id="status"
                    class="btn h-25 manager-{{$manager->status}}">{{$manager_statuses[$manager->status]}}
            </button>

            <form id="status-form" method="post" action="{{route('coordinator.manager.status',$manager)}}"
                  class="d-none">
                @csrf
                @method('patch')
                <select name="status" class="form-select w-100">
                    @foreach(array_keys($manager_statuses) as $status)
                        <option
                            {{$manager->status==$status?'selected':''}} value="{{$status}}">{{$manager_statuses[$status]}}</option>
                    @endforeach
                </select>
                <input type="submit"
                       class="btn btn-primary text-white w-50 rounded-2  p-2"
                       value="Изменить статус"/>
            </form>

            <button id="change-status"
                    class="btn btn-primary text-white rounded-2  p-2">Изменить статус
            </button>
            <script>
                document.getElementById('change-status').addEventListener('click', () => {
                    document.getElementById('change-status').className = 'd-none';
                    document.getElementById('status').className = 'd-none';
                    document.getElementById('status-form').className = 'd-flex flex-row gap-2'
                })
            </script>
            @endrole
            @role('director')
            <button id="status"
                    class="btn h-25 manager-{{$manager->status}}">{{$manager_statuses[$manager->status]}}
            </button>

            <form id="status-form" method="post" action="{{route('director.manager.status',$manager)}}"
                  class="d-none">
                @csrf
                @method('patch')
                <select name="status" class="form-select w-100">
                    @foreach(array_keys($manager_statuses) as $status)
                        <option
                            {{$manager->status==$status?'selected':''}} value="{{$status}}">{{$manager_statuses[$status]}}</option>
                    @endforeach
                </select>
                <input type="submit"
                       class="btn btn-primary text-white w-50 rounded-2  p-2"
                       value="Изменить статус"/>
            </form>

            <button id="change-status"
                    class="btn btn-primary text-white rounded-2  p-2">Изменить статус
            </button>
            <script>
                document.getElementById('change-status').addEventListener('click', () => {
                    document.getElementById('change-status').className = 'd-none';
                    document.getElementById('status').className = 'd-none';
                    document.getElementById('status-form').className = 'd-flex flex-row gap-2'
                })
            </script>
            @endrole
        </div>

        @role('manager')
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
        @endrole
        @role('director')
        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/manager/{{$manager->id}}?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/manager/{{$manager->id}}?date={{$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        @endrole


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
                    <th scope="col" class="fw-normal text-center">Встреч</th>
                    @foreach($days as $day)
                        @if($day['meetings']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['meetings']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalMeetings}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Успешных</th>
                    @foreach($days as $day)
                        @if($day['meetings']!=0)
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
                        @if($day['meetings']!=0)
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
                        @if($day['meetings']!=0)
                            <th class="fw-bold text-center" scope="col">✓</th>
                        @else
                            <th class="fw-normal text-center" scope="col">✕</th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalWorkDays}}/{{count($days)}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">ТО Продано</th>
                    @foreach($days as $day)
                        @if($day['meetings']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['products_selled']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalSelled}}</th>
                </tr>

                <tr>
                    <th scope="col" class="fw-normal text-center">ТО Оформлено</th>
                    @foreach($days as $day)
                        @if($day['meetings']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['products_issued']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalIssued}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">ТО Зачёт</th>
                    @foreach($days as $day)
                        @if($day['meetings']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['products_confirmed']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalConfirmed}}</th>
                </tr>


                </tbody>

            </table>


            <table class="table table-bordered table-sm table-secondary ">
                <thead>
                <tr>
                    <th class="fw-bold text-center" scope="col">% ТО</th>
                    <th class="fw-bold text-center" scope="col">% отказ</th>
                    <th class="fw-bold text-center" scope="col">Оклад</th>
                    <th class="fw-bold text-center" scope="col">Рабочих дней</th>
                    <th class="fw-bold text-center" scope="col">Факт. оклад</th>
                    <th class="fw-bold text-center" scope="col">Общ.сумма удержаний</th>
                    <th class="fw-bold text-center" scope="col">Сумма к выдаче</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="fw-normal text-center" scope="col">{{$totalConfirmed*0.2}}</th>
                    <th class="fw-normal text-center" scope="col">{{$totalConfirmed*0.05}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{$manager->bet?$manager->bet*count($days):'Не назначено'}}</th>
                    <th class="fw-normal text-center" scope="col">{{$totalWorkDays}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{$manager->bet?$manager->bet*$totalWorkDays:'-'}}</th>
                    <th class="fw-normal text-center" scope="col">0</th>
                    <th class="fw-normal text-center"
                        scope="col">{{($totalConfirmed*0.2)+($totalConfirmed*0.05)+($manager->bet?$manager->bet*$totalWorkDays:0)}}</th>
                </tr>
                </tbody>

            </table>


            @role('coordinator')

            <table class="table table-bordered mt-5 table-sm table-secondary">
                <thead class="table-light">
                <tr class="bg-light">
                    <th class="fw-bold text-left" scope="col">Ожидает</th>
                    <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                    <th class="fw-bold text-left" scope="col">Примечание</th>
                    <th class="fw-bold text-left" scope="col">Принял</th>
                    <th class="fw-bold text-left" scope="col">Вошел</th>
                    <th class="fw-bold text-left" scope="col">Вышел</th>
                    <th class="fw-bold text-left" scope="col">Сумма</th>
                    <th class="fw-bold text-left" scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($leads as $lead)
                    <tr class="p-3">
                        <th class="{{$lead->status=='declined'?"bg-declined":""}}  p-2 fw-bold text-left"
                            scope="col">
                            c {{preg_split("/[^1234567890]/", $lead->time_period)[0]}}
                            до {{preg_split("/[^1234567890]/", $lead->time_period)[1]}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">
                            <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$lead->client_fullname}}</p>
                            <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$lead->address}}</p>
                            <p class="mb-0 fw-normal"><strong>Телефон: </strong> {{$lead->phone}}</p>
                            <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$lead->comment}}</p>
                            <p class="mb-0 fw-normal"><strong>Тип работ:</strong> {{$lead->jobType->name}}</p>
                        </th>
                        <th class="p-2 fw-normal text-left" scope="col">
                            {{$lead->note}}
                        </th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->accepted}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->entered}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->exited}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->check}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">
                            @if($lead->status!='declined')
                                @if(!$lead->accepted)
                                    <form action="{{route('coordinator.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="accepted"/>
                                        <input type="submit" class="my-2 btn btn-success w-100" value="Принял">
                                    </form>

                                @elseif(!$lead->entered)
                                    <form id="accept" action="{{route('coordinator.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="entered"/>
                                        <input type="submit" class="btn btn-success w-100" value="Вошёл">
                                    </form>
                                    {{--                                        <form id="decline-form" class="d-none"--}}
                                    {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                    {{--                                              method="post">--}}
                                    {{--                                            @csrf--}}
                                    {{--                                            @method('PATCH')--}}
                                    {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                    {{--                                                   type="text"/>--}}
                                    {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                    {{--                                        </form>--}}
                                    {{--                                        <button id="decline" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                @elseif(!$lead->exited)
                                    <form action="{{route('coordinator.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="exited"/>
                                        <input type="submit" class="btn btn-success w-100" value="Вышел">
                                    </form>
                                @elseif(!$lead->check)
                                    {{--                                        <button id="success" class="my-2 btn btn-success w-100">Успешно</button>--}}
                                    {{--                                        <button id="otkaz" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                    {{--                                        <form id="decline-form-second" class="d-none"--}}
                                    {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                    {{--                                              method="post">--}}
                                    {{--                                            @csrf--}}
                                    {{--                                            @method('PATCH')--}}
                                    {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                    {{--                                                   type="text"/>--}}
                                    {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                    {{--                                        </form>--}}
                                    <form id="success-form" class="flex d-flex flex-column"
                                          enctype="multipart/form-data"
                                          action="{{route('coordinator.manager.leads.close',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-group d-flex flex-column">
                                            <label for="check">Сумма</label>
                                            <input class="form-control w-100 my-2" name="check" type="number"/>
                                            {{--                                                <label for="note">Примечание</label>--}}
                                            {{--                                                <textarea class="form-control w-100 my-2" name="note"--}}
                                            {{--                                                          type="text"></textarea>--}}
                                            {{--                                                <label for="documents">Документы</label>--}}
                                            {{--                                                <input enctype="multipart/form-data" type="file"--}}
                                            {{--                                                       class="my-2 form-control"--}}
                                            {{--                                                       name="documents[]"--}}
                                            {{--                                                       placeholder="Документы" multiple>--}}
                                            <input type="submit" class="btn btn-success w-auto"
                                                   value="Закрыть встречу">
                                        </div>
                                    </form>
                                @endif
                            @endif
                        </th>
                    </tr>
                @endforeach
                </tbody>

            </table>

            @endrole


            @role('director')

            <table class="table table-bordered mt-5 table-sm table-secondary">
                <thead class="table-light">
                <tr class="bg-light">
                    <th class="fw-bold text-left" scope="col">Ожидает</th>
                    <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                    <th class="fw-bold text-left" scope="col">Примечание</th>
                    <th class="fw-bold text-left" scope="col">Принял</th>
                    <th class="fw-bold text-left" scope="col">Вошел</th>
                    <th class="fw-bold text-left" scope="col">Вышел</th>
                    <th class="fw-bold text-left" scope="col">Сумма</th>
                    <th class="fw-bold text-left" scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($leads as $lead)
                    <tr class="p-3">
                        <th class="{{$lead->status=='declined'?"bg-declined":""}}  p-2 fw-bold text-left"
                            scope="col">
                            c {{preg_split("/[^1234567890]/", $lead->time_period)[0]}}
                            до {{preg_split("/[^1234567890]/", $lead->time_period)[1]}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">
                            <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$lead->client_fullname}}</p>
                            <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$lead->address}}</p>
                            <p class="mb-0 fw-normal"><strong>Телефон: </strong> {{$lead->phone}}</p>
                            <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$lead->comment}}</p>
                            <p class="mb-0 fw-normal"><strong>Тип работ:</strong> {{$lead->jobType->name}}</p>
                        </th>
                        <th class="p-2 fw-normal text-left" scope="col">
                            {{$lead->note}}
                        </th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->accepted}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->entered}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->exited}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">{{$lead->check}}</th>
                        <th class="p-2 fw-bold text-left" scope="col">
                            @if($lead->status!='declined')
                                @if(!$lead->accepted)
                                    <form action="{{route('director.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="accepted"/>
                                        <input type="submit" class="my-2 btn btn-success w-100" value="Принял">
                                    </form>

                                @elseif(!$lead->entered)
                                    <form id="accept" action="{{route('director.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="entered"/>
                                        <input type="submit" class="btn btn-success w-100" value="Вошёл">
                                    </form>
                                    {{--                                        <form id="decline-form" class="d-none"--}}
                                    {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                    {{--                                              method="post">--}}
                                    {{--                                            @csrf--}}
                                    {{--                                            @method('PATCH')--}}
                                    {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                    {{--                                                   type="text"/>--}}
                                    {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                    {{--                                        </form>--}}
                                    {{--                                        <button id="decline" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                @elseif(!$lead->exited)
                                    <form action="{{route('director.manager.leads.status',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input class="" name="status" type="hidden"
                                               value="exited"/>
                                        <input type="submit" class="btn btn-success w-100" value="Вышел">
                                    </form>
                                @elseif(!$lead->check)
                                    {{--                                        <button id="success" class="my-2 btn btn-success w-100">Успешно</button>--}}
                                    {{--                                        <button id="otkaz" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                    {{--                                        <form id="decline-form-second" class="d-none"--}}
                                    {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                    {{--                                              method="post">--}}
                                    {{--                                            @csrf--}}
                                    {{--                                            @method('PATCH')--}}
                                    {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                    {{--                                                   type="text"/>--}}
                                    {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                    {{--                                        </form>--}}
                                    <form id="success-form" class="flex d-flex flex-column"
                                          enctype="multipart/form-data"
                                          action="{{route('director.manager.leads.close',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-group d-flex flex-column">
                                            <label for="check">Сумма</label>
                                            <input class="form-control w-100 my-2" name="check" type="number"/>
                                            {{--                                                <label for="note">Примечание</label>--}}
                                            {{--                                                <textarea class="form-control w-100 my-2" name="note"--}}
                                            {{--                                                          type="text"></textarea>--}}
                                            {{--                                                <label for="documents">Документы</label>--}}
                                            {{--                                                <input enctype="multipart/form-data" type="file"--}}
                                            {{--                                                       class="my-2 form-control"--}}
                                            {{--                                                       name="documents[]"--}}
                                            {{--                                                       placeholder="Документы" multiple>--}}
                                            <input type="submit" class="btn btn-success w-auto"
                                                   value="Закрыть встречу">
                                        </div>
                                    </form>
                                @endif
                            @endif
                        </th>
                    </tr>
                @endforeach
                </tbody>

            </table>

            @endrole


            {{--            <div class="bd-cyan-500">--}}
            {{--                <table class="table table-bordered table-sm table-secondary ">--}}
            {{--                    <thead class="">--}}
            {{--                    <tr>--}}
            {{--                        <th class="fw-bold text-left" scope="col">Премия</th>--}}
            {{--                        <th class="fw-bold text-left" scope="col">За заявки</th>--}}
            {{--                        <th class="fw-bold text-left" scope="col">За выходы</th>--}}
            {{--                        <th class="fw-bold text-left" scope="col">Рабочих дней</th>--}}
            {{--                        <th class="fw-bold text-left" scope="col">Общая сумма удержаний</th>--}}
            {{--                        <th class="fw-bold text-left" scope="col">Сумма к выдаче</th>--}}

            {{--                    </tr>--}}
            {{--                    </thead>--}}
            {{--                    <tbody>--}}
            {{--                    <tr>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            0--}}
            {{--                        </th>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            {{$totalSuccessful*120}}--}}
            {{--                        </th>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            {{$totalWorkDays*200}}--}}
            {{--                        </th>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            {{$totalWorkDays}}--}}
            {{--                        </th>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            0--}}
            {{--                        </th>--}}
            {{--                        <th class="fw-normal text-left" scope="col">--}}
            {{--                            {{$user->salary}}--}}
            {{--                        </th>--}}

            {{--                    </tr>--}}
            {{--                    </tbody>--}}

            {{--                </table>--}}
            {{--            </div>--}}

        </div>

    </div>
@endsection
