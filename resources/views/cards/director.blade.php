@extends('layouts.app')
@section('title')
    {{'- Карточка'}}
@endsection
@section('content')
    <div class="container">
        <div class="d-flex align-items-center gap-3 my-3">
            <p class="fw-bold mb-0 fs-3">{{$director->name}}</p>
            @role('director')

            @if(\Illuminate\Support\Facades\Auth::user()->isAdmin)
                <button id="checkdocs"
                        class="btn btn-warning text-white rounded-2  p-2">Документы
                </button>
            @endif

            @endrole
        </div>

        @role('director')
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
                   href="{{route('director.directorcard',$director->id).'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.directorcard',$director->id).'?date='.$nextMonthLink}}">Следующий
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
                    <th scope="col" class="fw-normal text-center">{{$city->name}} ТО зач.</th>
                    @foreach($days as $day)
                        @if($day['repairs_confirmed']!=0)
                            <th class="fw-normal text-center" scope="col">{{$day['repairs_confirmed']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">{{$totalConfirmed}}</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">{{$city->name}} КМ</th>
                    @foreach($days as $day)
                        @if($day['managers'])
                            <th class="fw-normal text-center" scope="col">{{count($day['managers'])}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col"></th>
                        @endif
                    @endforeach
                    <th class="fw-normal  text-center" scope="row">-</th>
                </tr>
                <tr>
                    <th scope="col" class="fw-normal text-center">Раб. день</th>
                    @foreach($days as $day)
                        @if($day['workDay']!=0)
                            <th class="fw-bold text-center" scope="col">
                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin)
                                    <form action="{{route('director.delete.workday',$director)}}" method="post"
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
                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin)
                                    <form method="post" action="{{route('director.add.workday',$director)}}"
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
                </tbody>
            </table>


            <table class="table table-bordered table-sm table-secondary ">
                <thead>
                <tr>
                    <th class="fw-bold text-center" scope="col">% ТО</th>
                    <th class="fw-bold text-center" scope="col">Оклад</th>
                    <th class="fw-bold text-center" scope="col">Рабочих дней</th>
                    <th class="fw-bold text-center" scope="col">Фактический оклад</th>
                    <th class="fw-bold text-center" scope="col">Общ.сумма удержаний</th>
                    <th class="fw-bold text-center" scope="col">Сумма к выдаче</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th class="fw-normal text-center"
                        scope="col">{{$totalConfirmed>=1000000?$totalConfirmed*0.01:0}}</th>
                    <th class="fw-normal text-center" scope="col">50000</th>
                    <th class="fw-normal text-center" scope="col">{{$totalWorkDays}}</th>
                    <th class="fw-normal text-center"
                        scope="col">{{round($totalWorkDays*50000/(count($days)-$weekends))}}</th>
                    <th class="fw-normal text-center" scope="col">0</th>
                    <th class="fw-normal text-center"
                        scope="col">{{round($totalWorkDays*50000/(count($days)-$weekends))}}</th>
                </tr>
                </tbody>

            </table>


        </div>

    </div>
@endsection
