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
            {{--            <button id="status"--}}
            {{--                    class="btn h-25 manager-{{$manager->status}}">{{$manager_statuses[$manager->status]}}--}}
            {{--            </button>--}}

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

            {{--            <button id="change-status"--}}
            {{--                    class="btn btn-primary text-white rounded-2  p-2">Изменить статус--}}
            {{--            </button>--}}

            <button id="checkdocs"
                    class="btn btn-warning text-white rounded-2  p-2">Документы
            </button>

            <button onclick="showBonuses()"
                    class="btn btn-secondary text-white rounded-2  p-2">Бонусы и удержания
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
                   href="{{route('director.managercard',$manager->id).'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.managercard',$manager->id).'?date='.$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        @endrole
        @role('director')
        <div id="docs-pop" class="d-none">
            @foreach($documents as $document)
                <a href="{{ URL::to('/documents') }}/{{$document}}"
                   class="w-100 border border-2 border-black d-flex"><img
                        class="w-100 object-fit-cover"
                        src="{{ URL::to('/documents') }}/{{$document}}"/></a>
            @endforeach
        </div>
        <div class="d-none" id="bonuses">
            <div class="d-flex flex-column">
                <p class="w-100 text-center fw-bold fs-4">Бонусы</p>
                <table class="table table-bordered table-sm table-light ">
                    <thead class="">
                    <tr>
                        <th class="fw-bold p-2 text-left" scope="col">Дата</th>
                        <th class="fw-bold p-2 text-left" scope="col">Обоснование</th>
                        <th class="fw-bold p-2 text-left" scope="col">Сумма</th>
                        <th class="fw-bold p-2 text-left" scope="col"></th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bonuses as $bonus)
                        <tr>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$bonus->created_at}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$bonus->reason}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$bonus->amount}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <form method="post" action="{{route('director.bonus.delete',$bonus)}}">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" class="btn btn-danger" value="Удалить"/>
                                </form>
                            </th>
                        </tr>
                    @endforeach
                    <tr>
                        <form method="post" action="{{route('director.bonuses.create',$manager)}}">
                            @csrf
                            @method('post')
                            <input type="hidden" name="type" value="plus"/>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{\Carbon\Carbon::today()->toDateString()}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input placeholder="Введите обоснование" type="text" name="reason"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input placeholder="Введите сумму" type="number" name="amount"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input type="submit" class="btn btn-success" value="Выдать"/>
                            </th>
                        </form>
                    </tr>
                    </tbody>

                </table>
            </div>
            <div class="d-flex flex-column">
                <p class="w-100 text-center fw-bold fs-4">Удержания</p>
                <table class="table table-bordered table-sm table-light ">
                    <thead class="">
                    <tr>
                        <th class="fw-bold p-2 text-left" scope="col">Дата</th>
                        <th class="fw-bold p-2 text-left" scope="col">Обоснование</th>
                        <th class="fw-bold p-2 text-left" scope="col">Сумма</th>
                        <th class="fw-bold p-2 text-left" scope="col"></th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($deductions as $deduction)
                        <tr>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$deduction->created_at}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$deduction->reason}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{$deduction->amount}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <form method="post" action="{{route('director.bonus.delete',$deduction)}}">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" class="btn btn-danger" value="Удалить"/>
                                </form>
                            </th>
                        </tr>
                    @endforeach
                    <tr>
                        <form method="post" action="{{route('director.bonuses.create',$manager)}}">
                            @csrf
                            @method('post')
                            <input type="hidden" name="type" value="minus"/>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{\Carbon\Carbon::today()->toDateString()}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input placeholder="Введите обоснование" type="text" name="reason"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input placeholder="Введите сумму" type="number" name="amount"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input type="submit" class="btn btn-danger" value="Удержать"/>
                            </th>
                        </form>
                    </tr>
                    </tbody>

                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.managercard',$manager->id).'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.managercard',$manager->id).'?date='.$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        <script>
            let isOpen = false;
            document.getElementById('checkdocs').addEventListener('click', () => {
                if (!isOpen) {
                    document.getElementById('docs-pop').className = 'w-100 d-flex justify-content-center my-4'
                    isOpen = true;
                } else {
                    document.getElementById('docs-pop').className = 'd-none'
                    isOpen = false;
                }
            })

            isBonusesOpen = false;

            function showBonuses() {
                if (!isBonusesOpen) {
                    document.getElementById('bonuses').className = 'w-100 row bg-secondary-subtle m-0 p-4 row-cols-2 my-4'
                    isBonusesOpen = true;
                } else {
                    document.getElementById('bonuses').className = 'd-none'
                    isBonusesOpen = false;
                }
            }

        </script>
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
                        @if($day['workDay']!=0)
                            <th class="fw-bold text-center" scope="col">
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('director'))
                                    <form action="{{route('director.delete.workday',$manager)}}" method="post"
                                          class="w-auto d-flex flex-column align-items-center">
                                        @csrf
                                        @method('delete')
                                        <input name="date" value="{{$day['date']}}" type="hidden">
                                        <input class="p-0 bg-transparent border-0" type="submit" value="✔️">
                                    </form>
                                @else
                                    <p>✔️</p>
                                @endif
                            </th>
                        @else
                            <th class="fw-normal text-center" scope="col">
                                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('director'))
                                    <form method="post" action="{{route('director.add.workday',$manager)}}"
                                          class="w-auto d-flex flex-column align-items-center">
                                        @csrf
                                        @method('post')
                                        <input name="date" value="{{$day['date']}}" type="hidden">
                                        <input class="p-0 bg-transparent border-0" type="submit" value="❌">
                                    </form>
                                @else
                                    <p>❌</p>
                                @endif
                            </th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalWorkDays}}/{{(count($days)-$weekends)}}</th>
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
{{--                    <th class="fw-bold text-center" scope="col">Общ.сумма бонусов</th>--}}
                    <th class="fw-bold text-center" scope="col">Общ.сумма удержаний</th>
                    <th class="fw-bold text-center" scope="col">Сумма к выдаче</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="fw-normal text-center"
                        scope="col">{{$totalConfirmed*$totalProductsPercent-($totalDeclined<3?$totalConfirmed*0.01:0)}}</th>
                    <th class="fw-normal text-center" scope="col">{{$totalDeclined<3?$totalConfirmed*0.01:0}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{$oklad}}</th>
                    <th class="fw-normal text-center" scope="col">{{$totalWorkDays}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{round($okladSallary)}}</th>
{{--                    <th class="fw-normal text-center" scope="col">{{$totalBonus}}</th>--}}
                    <th class="fw-normal text-center" scope="col">{{$totalDeduction}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{round($manager->salary($date))}}</th>
                </tr>
                </tbody>

            </table>


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
