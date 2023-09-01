@extends('layouts.app')

@section('title')
    {{'- Таблица контроля'}}
@endsection

@section('content')
    <div class="container">


        @role('coordinator')
        <div class="">
            <div class="">
                @foreach($cities as $city)
                    <button
                        class="btn {{$city->id==$city_id?'btn-secondary':'btn-outline-secondary'}} "
                        onclick="window.location.href='/coordinator/?city={{$city->id}}'">{{$city->name}}
                    </button>
                @endforeach
                <div class="container mt-3 row">
                    <div class="col-sm d-flex flex-column">
                        <p class="fs-3">{{$month}}</p>
                        <table class="table table-bordered table-sm table-secondary">
                            <thead class="table-light">
                            <tr class="bg-light">
                                <th class="fw-bold text-left" scope="col">Встречи</th>
                                <th class="fw-bold text-left" scope="col">ДОРы</th>
                                <th class="fw-bold text-left" scope="col">Отказы</th>
                                <th class="fw-bold text-left" scope="col">Т.О Продано</th>
                                <th class="fw-bold text-left" scope="col">Т.О Зачёт</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="fw-bold text-left" scope="col">{{count($leads)}}</th>
                                <th class="fw-bold text-left" scope="col">-</th>
                                <th class="fw-bold text-left" scope="col">{{count($declined)}}</th>
                                <th class="fw-bold text-left" scope="col">{{$products_selled}}</th>
                                <th class="fw-bold text-left" scope="col">0</th>

                            </tr>
                            </tbody>

                        </table>
                    </div>
                    <div class="col-sm d-flex flex-column">
                        <p class="fs-3">Сегодня</p>
                        <table class="table table-bordered table-sm table-secondary">
                            <thead class="table-light">
                            <tr class="bg-light">
                                <th class="fw-bold text-left" scope="col">Встречи</th>
                                <th class="fw-bold text-left" scope="col">ДОРы</th>
                                <th class="fw-bold text-left" scope="col">Отказы</th>
                                <th class="fw-bold text-left" scope="col">Т.О Продано</th>
                                <th class="fw-bold text-left" scope="col">Т.О Зачёт</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="fw-bold text-left" scope="col">{{count($todayLeads)}}</th>
                                <th class="fw-bold text-left" scope="col">-</th>
                                <th class="fw-bold text-left" scope="col">{{count($todayDeclined)}}</th>
                                <th class="fw-bold text-left" scope="col">{{$todayProductsSelled}}</th>
                                <th class="fw-bold text-left" scope="col">0</th>

                            </tr>
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="d-flex my-4 flex-row justify-content-center gap-3">
                    <button class="btn manager-free">свободен</button>
                    <button class="btn manager-dinner">обед</button>
                    <button class="btn manager-weekend">выходной</button>
                    <button class="btn manager-meeting-managed">встреча назначена</button>
                    <button class="btn manager-meeting-accepted">встреча принята</button>
                    <button class="btn manager-on-meeting">на встрече</button>
                    <button class="btn manager-delaying">задерживается</button>

                </div>

                <div class="d-flex flex-row gap-2 align-items-center justify-content-center">
                    <p class="fs-1 {{$plan?'text-black':'text-danger'}} m-0">
                        План: {{$plan?$plan->value:'Не назначено'}}
                        ({{$products_selled}}) {{$plan&&$plan->value<$products_selled?'✅':''}}</p>
                </div>

                <p class="text-center w-100 fs-1">Список менеджеров</p>
                @foreach($managers as $manager)
                    <button onclick="window.location.href='{{route('coordinator.managercard',$manager)}}'"
                            class="btn manager-{{$manager->status}}">{{$manager->name}}</button>
                @endforeach

                <table class="table table-bordered mt-3 table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Ожидает</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col"></th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($todayLeads as $lead)
                        <tr>
                            <th class="fw-bold {{$lead->check?"bg-completed":""}} text-left" scope="col">
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
                                @if(!$lead->manager_id)
                                    <div id="control-panel" class="d-flex flex-row gap-2">
                                        <div class="d-flex flex-row gap-2 w-50">
                                            <form method="post" action="{{route('coordinator.leads.manage',$lead)}}"
                                                  class="d-flex flex-row gap-2">
                                                @csrf
                                                @method('patch')
                                                <select name="manager" class="form-select w-100">
                                                    @foreach($managers as $manager)
                                                        @if($manager->status=='free')
                                                            <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input type="submit"
                                                       class="btn btn-primary text-white w-50 rounded-2  p-2"
                                                       value="Назначить"/>
                                            </form>
                                        </div>
                                        <div class="d-flex flex-row gap-2 w-50">
                                            <button id="decline-btn"
                                                    class="btn btn-danger text-white w-50 rounded-2  p-2">
                                                Отказ
                                            </button>
                                            <button
                                                onclick="window.location='{{route('coordinator.leads.edit',$lead->id)}}'"
                                                class="btn btn-warning text-white w-75 rounded-2  p-2">
                                                Редатировать
                                            </button>
                                        </div>

                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-0">
                                        <p class="m-0">Принял: {{$lead->accepted}}</p>
                                        <p class="m-0">Вошёл: {{$lead->entered}}</p>
                                        <p class="m-0">Вышел: {{$lead->exited}}</p>
                                        <p class="m-0">Менеджер: {{$lead->getManagerId->name}}</p>
                                        <p class="m-0">Сумма: {{$lead->check}}</p>
                                    </div>
                                    <div class="d-flex">
                                        <div id="control-panel" class="d-flex flex-row gap-2">
                                            <div class="d-flex flex-row gap-2 w-100">
                                                <button id="decline-btn"
                                                        class="btn btn-danger text-white w-50 rounded-2  p-2">
                                                    Отказ
                                                </button>
                                                <button
                                                    onclick="window.location='{{route('coordinator.leads.edit',$lead->id)}}'"
                                                    class="btn btn-warning text-white w-75 rounded-2  p-2">
                                                    Редатировать
                                                </button>
                                                <form method="post"
                                                      action="{{route('coordinator.leads.changemanager',$lead)}}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" name="manager" value="{{$lead->manager_id}}">
                                                    <input type="submit"
                                                           class="btn btn-primary text-white w-100 rounded-2  p-2"
                                                           value="Сменить менеджера"/>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                @endif


                                <div id="decline-form-second" class="d-none">
                                    <form class="d-flex gap-2 w-100"
                                          action="{{route('coordinator.leads.decline',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input placeholder="Укажите причину отказа" class="form-control" name="note"
                                               type="text"/>
                                        <input type="submit" class="my-2 btn btn-danger w-50" value="Отказ">
                                    </form>
                                </div>
                                <p class="fw-normal">Оператор: {{$lead->getOperatorId->name}}</p>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>
        @endrole


        @role('director')
        <div class="">
            <div class="">
                @foreach($cities as $city)
                    <button
                        class="btn {{$city->id==$city_id?'btn-secondary':'btn-outline-secondary'}} "
                        onclick="window.location.href='/director/?city={{$city->id}}'">{{$city->name}}
                    </button>
                @endforeach
                <div class="container mt-3 row">
                    <div class="col-sm d-flex flex-column">
                        <p class="fs-3">{{$month}}</p>
                        <table class="table table-bordered table-sm table-secondary">
                            <thead class="table-light">
                            <tr class="bg-light">
                                <th class="fw-bold text-left" scope="col">Встречи</th>
                                <th class="fw-bold text-left" scope="col">ДОРы</th>
                                <th class="fw-bold text-left" scope="col">Отказы</th>
                                <th class="fw-bold text-left" scope="col">Т.О Продано</th>
                                <th class="fw-bold text-left" scope="col">Т.О Зачёт</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="fw-bold text-left" scope="col">{{count($leads)}}</th>
                                <th class="fw-bold text-left" scope="col">-</th>
                                <th class="fw-bold text-left" scope="col">{{count($declined)}}</th>
                                <th class="fw-bold text-left" scope="col">{{$products_selled}}</th>
                                <th class="fw-bold text-left" scope="col">0</th>

                            </tr>
                            </tbody>

                        </table>
                    </div>
                    <div class="col-sm d-flex flex-column">
                        <p class="fs-3">Сегодня</p>
                        <table class="table table-bordered table-sm table-secondary">
                            <thead class="table-light">
                            <tr class="bg-light">
                                <th class="fw-bold text-left" scope="col">Встречи</th>
                                <th class="fw-bold text-left" scope="col">ДОРы</th>
                                <th class="fw-bold text-left" scope="col">Отказы</th>
                                <th class="fw-bold text-left" scope="col">Т.О Продано</th>
                                <th class="fw-bold text-left" scope="col">Т.О Зачёт</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="fw-bold text-left" scope="col">{{count($todayLeads)}}</th>
                                <th class="fw-bold text-left" scope="col">-</th>
                                <th class="fw-bold text-left" scope="col">{{count($todayDeclined)}}</th>
                                <th class="fw-bold text-left" scope="col">{{$todayProductsSelled}}</th>
                                <th class="fw-bold text-left" scope="col">0</th>

                            </tr>
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="d-flex my-4 flex-row justify-content-center gap-3">
                    <button class="btn manager-free">свободен</button>
                    <button class="btn manager-dinner">обед</button>
                    <button class="btn manager-weekend">выходной</button>
                    <button class="btn manager-meeting-managed">встреча назначена</button>
                    <button class="btn manager-meeting-accepted">встреча принята</button>
                    <button class="btn manager-on-meeting">на встрече</button>
                    <button class="btn manager-delaying">задерживается</button>

                </div>
                <div class="d-flex flex-row gap-2 align-items-center justify-content-center">
                    <p class="fs-1 {{$plan?'text-black':'text-danger'}} m-0">
                        План: {{$plan?$plan->value:'Не назначено'}}
                        ({{$products_selled}}) {{$plan&&$plan->value<$products_selled?'✅':''}}</p>
                    <button id="change-plan-btn" class="btn h-50 fw-bold btn-warning">Изменить
                        план {{$city_id}}</button>
                    <form action="{{route('director.changeplan')}}" method="post" id="change-plan-form" class="d-none">
                        @csrf
                        @method('patch')
                        <input placeholder="Введите новый план" name="value" type="number" class="form-control"/>
                        <input name="city" type="hidden" value="{{$city_id}}">
                        <input type="submit" value="Изменить" class="btn h-50 fw-bold btn-warning"/>
                    </form>
                </div>
                <p class="text-center w-100 fs-1">Список менеджеров</p>
                @foreach($managers as $manager)
                    <button onclick="window.location.href='{{route('director.managercard',$manager)}}'"
                            class="btn manager-{{$manager->status}}">{{$manager->name}}</button>
                @endforeach

                <table class="table table-bordered mt-3 table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Ожидает</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col"></th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($todayLeads as $lead)
                        <tr>
                            <th class="fw-bold {{$lead->check?"bg-completed":""}} text-left" scope="col">
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
                                @if(!$lead->manager_id)
                                    <div id="control-panel" class="d-flex flex-row gap-2">
                                        <div class="d-flex flex-row gap-2 w-50">
                                            <form method="post" action="{{route('director.leads.manage',$lead)}}"
                                                  class="d-flex flex-row gap-2">
                                                @csrf
                                                @method('patch')
                                                <select name="manager" class="form-select w-100">
                                                    @foreach($managers as $manager)
                                                        @if($manager->status=='free')
                                                            <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <input type="submit"
                                                       class="btn btn-primary text-white w-50 rounded-2  p-2"
                                                       value="Назначить"/>
                                            </form>
                                        </div>
                                        <div class="d-flex flex-row gap-2 w-50">
                                            <button id="decline-btn"
                                                    class="btn btn-danger text-white w-50 rounded-2  p-2">
                                                Отказ
                                            </button>
                                            <button
                                                onclick="window.location='{{route('director.leads.edit',$lead->id)}}'"
                                                class="btn btn-warning text-white w-75 rounded-2  p-2">
                                                Редатировать
                                            </button>
                                        </div>

                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-0">
                                        <p class="m-0">Принял: {{$lead->accepted}}</p>
                                        <p class="m-0">Вошёл: {{$lead->entered}}</p>
                                        <p class="m-0">Вышел: {{$lead->exited}}</p>
                                        <p class="m-0">Менеджер: {{$lead->getManagerId->name}}</p>
                                        <p class="m-0">Сумма: {{$lead->check}}</p>
                                    </div>
                                    <div class="d-flex">
                                        <div id="control-panel" class="d-flex flex-row gap-2">
                                            <div class="d-flex flex-row gap-2 w-100">
                                                <button id="decline-btn"
                                                        class="btn btn-danger text-white w-50 rounded-2  p-2">
                                                    Отказ
                                                </button>
                                                <button
                                                    onclick="window.location='{{route('director.leads.edit',$lead->id)}}'"
                                                    class="btn btn-warning text-white w-75 rounded-2  p-2">
                                                    Редатировать
                                                </button>
                                                <form method="post"
                                                      action="{{route('director.leads.changemanager',$lead)}}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" name="manager" value="{{$lead->manager_id}}">
                                                    <input type="submit"
                                                           class="btn btn-primary text-white w-100 rounded-2  p-2"
                                                           value="Сменить менеджера"/>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                @endif


                                <div id="decline-form-second" class="d-none">
                                    <form class="d-flex gap-2 w-100"
                                          action="{{route('director.leads.decline',$lead->id)}}"
                                          method="post">
                                        @csrf
                                        @method('PATCH')
                                        <input placeholder="Укажите причину отказа" class="form-control" name="note"
                                               type="text"/>
                                        <input type="submit" class="my-2 btn btn-danger w-50" value="Отказ">
                                    </form>
                                </div>
                                <p class="fw-normal">Оператор: {{$lead->getOperatorId->name}}</p>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>
        @endrole

        <script>
            document.getElementById('decline-btn')?.addEventListener('click', () => {
                document.getElementById('control-panel').className = 'd-none';
                document.getElementById('decline-form-second').className = 'd-flex flex-row gap-2';
            })

            document.getElementById('change-plan-btn')?.addEventListener('click', () => {
                document.getElementById('change-plan-btn').className = 'd-none';
                document.getElementById('change-plan-form').className = 'd-flex gap-2';

            })
        </script>

    </div>
@endsection
