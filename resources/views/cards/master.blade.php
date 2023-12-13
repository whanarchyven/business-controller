@extends('layouts.app')
@section('title')
    {{'- Карточка'}}
@endsection
@section('content')
    <div class="container">
        <div class="d-flex align-items-center gap-3 my-3">
            <p class="fw-bold mb-0 fs-3">{{$master->name}}</p>
            @role('director')

            <button id="checkdocs"
                    class="btn btn-warning text-white rounded-2  p-2">Документы
            </button>
            <button onclick="showBonuses()"
                    class="btn btn-secondary text-white rounded-2  p-2">Бонусы и удержания
            </button>

            @endrole
        </div>

        @role('master')
        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.mastercard',$master->id).'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.mastercard',$master->id).'?date='.$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        @endrole
        @role('director')
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
                        <form method="post" action="{{route('director.bonuses.create',$master)}}">
                            @csrf
                            @method('post')
                            <input type="hidden" name="type" value="plus"/>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{\Carbon\Carbon::today()->toDateString()}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input required placeholder="Введите обоснование" type="text" name="reason"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input required placeholder="Введите сумму" type="number" name="amount"
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
                        <form method="post" action="{{route('director.bonuses.create',$master)}}">
                            @csrf
                            @method('post')
                            <input type="hidden" name="type" value="minus"/>
                            <th class="fw-normal p-2 text-left" scope="col">
                                {{\Carbon\Carbon::today()->toDateString()}}
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input required placeholder="Введите обоснование" type="text" name="reason"
                                       class="form-control"/>
                            </th>
                            <th class="fw-normal p-2 text-left" scope="col">
                                <input required placeholder="Введите сумму" type="number" name="amount"
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
        <div id="docs-pop" class="d-none">
            @foreach($documents as $document)
                <a href="{{ URL::to('/documents') }}/{{$document}}"
                   class="w-100 border border-2 border-black d-flex"><img
                        class="w-100 object-fit-cover"
                        src="{{ URL::to('/documents') }}/{{$document}}"/></a>
            @endforeach
        </div>
        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.mastercard',$master->id).'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.mastercard',$master->id).'?date='.$nextMonthLink}}">Следующий
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
                    <th scope="col" class="fw-normal text-center">Ремонтов</th>
                    @foreach($days as $day)
                        @if($day['repairs']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['repairs']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalRepairs}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Успешных</th>
                    @foreach($days as $day)
                        @if($day['repairs']!=0)
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
                        @if($day['repairs']!=0)
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
                        @if($day['repairs_confirmed']!=0)
                            <th class="fw-bold text-center" scope="col">✓</th>
                        @else
                            <th class="fw-normal text-center" scope="col">✕</th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalWorkDays}}/{{count($days)}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">ТО Зачёт</th>
                    @foreach($days as $day)
                        @if($day['repairs']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['repairs_confirmed']}}</th>
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
                    <th class="fw-bold text-center" scope="col">Рабочих дней</th>
                    <th class="fw-bold text-center" scope="col">Общ.сумма удержаний</th>
                    <th class="fw-bold text-center" scope="col">Сумма к выдаче</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="fw-normal text-center" scope="col">{{$master->salary($date)}}</th>
                    <th class="fw-normal text-center" scope="col">{{$totalWorkDays}}</th>
                    <th class="fw-normal text-center" scope="col">{{$master->deductions($date)}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{$master->salary($date)}}</th>
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
