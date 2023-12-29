@extends('layouts.app')

@section('title')
    {{'- ГСМ'}}
@endsection

@section('content')
    <div class="container">

{{--        <div class="d-flex justify-content-between">--}}
{{--            --}}
{{--            <div id="date-head">--}}
{{--                <p class="fs-3">{{$dateTitle}}</p>--}}
{{--            </div>--}}
{{--            --}}
{{--        </div>--}}

        <div class="d-flex justify-content-between">

            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/gsm{{$role=='manager'?'/managers':'/masters'}}?date={{\Carbon\Carbon::createFromDate($date)->subWeek()->toDateString()}}&day={{$prevMonday}}">Предыдущая
                    неделя</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="/director/gsm{{$role=='manager'?'/managers':'/masters'}}?date={{\Carbon\Carbon::createFromDate($date)->addWeek()->toDateString()}}&day={{$nextMonday}}">Следующая
                    неделя</a>
            </div>

        </div>

        <div class="">
            <table class="table table-bordered table-sm table-secondary ">
                <thead>
                <tr>
                    <th class="fw-normal text-center" scope="col">Менеджер</th>
                    @foreach($days as $day)
                        @if($day['weekDay']=='вс')
                            <th class="fw-normal text-center text-danger"
                                scope="col">{{$day['day']}} {{$day['weekDay']}}</th>
                        @else
                            <th class="fw-normal text-center" scope="col">{{$day['day']}} {{$day['weekDay']}}</th>
                        @endif
                    @endforeach
                    <th class="fw-normal text-center" scope="col">Итого</th>
                </tr>
                </thead>
                <tbody>
                @foreach($managers_gsm as $mg)
                    <tr class="gsm_row{{$loop->index}} border-2 border-black my-3">
                        <th class="fw-normal p-3 text-center" scope="col">{{$mg[0]->shortname()}}</th>
                        @foreach($mg[1] as $mg_day)
                            @if($mg_day['gsm']==0)
                                <th class="fw-normal p-3 text-center" scope="col">
                                    <div class="d-flex flex-column gap-3">
                                        @if($mg[0]->hasRole('master'))
                                            <form method="post" class="w-100" action="{{route('director.gsm.add')}}">
                                                @csrf
                                                @method('post')
                                                <input type="hidden" name="amount" value="{{($mg_day['repairs']>1?$mg_day["repairs"]*100-100:0)}}">
                                                <input type="hidden" name="city" value={{$city->id}}>
                                                <input type="hidden" name="manager" value={{$mg[0]->id}}>
                                                <input type="hidden" name="date" value={{$mg_day['date']}}>
                                                <input class="btn btn-primary" {{($mg_day['repairs']>1?'':'disabled')}} type="submit" value="Стандарт">
                                            </form>
                                        @endif
                                            @if($mg[0]->hasRole('manager'))
                                                <form method="post" class="w-100" action="{{route('director.gsm.add')}}">
                                                    @csrf
                                                    @method('post')
                                                    <input type="hidden" name="amount" value="{{($mg_day['repairs']>=1?250:0)}}">
                                                    <input type="hidden" name="city" value={{$city->id}}>
                                                    <input type="hidden" name="manager" value={{$mg[0]->id}}>
                                                    <input type="hidden" name="date" value={{$mg_day['date']}}>
                                                    <input class="btn btn-primary" {{($mg_day['repairs']>=1?'':'disabled')}} type="submit" value="Стандарт">
                                                </form>
                                            @endif
                                        @if($mg[0]->hasRole('master'))
                                            <form method="post" class="w-100" action="{{route('director.gsm.add')}}">
                                                @csrf
                                                @method('post')
                                                <input required
                                                       class="form-control mb-2" placeholder="{{$mg_day['repairs']>1?$mg_day["repairs"]*100-100:0}}" type="number"
                                                       name="amount">
                                                <input type="hidden" name="city" value={{$city->id}}>
                                                <input type="hidden" name="manager" value={{$mg[0]->id}}>
                                                <input type="hidden" name="date" value={{$mg_day['date']}}>
                                                <input class="btn btn-secondary" type="submit" value="Другое">
                                            </form>
                                        @endif
                                        @if($mg[0]->hasRole('manager'))
                                            <form method="post" class="w-100" action="{{route('director.gsm.add')}}">
                                                @csrf
                                                @method('post')
                                                <input required
                                                       class="form-control mb-2" placeholder="{{$mg_day['repairs']>=1?250:0}}" type="number"
                                                       name="amount">
                                                <input type="hidden" name="city" value={{$city->id}}>
                                                <input type="hidden" name="manager" value={{$mg[0]->id}}>
                                                <input type="hidden" name="date" value={{$mg_day['date']}}>
                                                <input class="btn btn-secondary" type="submit" value="Другое">
                                            </form>
                                        @endif
                                        <p>{{$mg[0]->hasRole('master')?'Ремонты: ':'Встречи: '}} {{$mg_day['repairs']}}</p>
                                    </div>
                                </th>
                            @else
                                <th class="fw-normal text-center" scope="col">
                                    <div class="d-flex flex-column">
                                        <p>{{$mg_day['gsm']}}</p>
                                        @if($mg_day['gsm_is_payed'])
                                            <p>Выдано</p>
                                        @else
                                            <form method="post" action="{{route('director.gsm.pay',$mg_day['gsm_id'])}}">
                                                @csrf
                                                @method('patch')
                                                <input name="role" type="hidden" value="{{$mg[0]->hasRole('manager')?'manager':'master'}}">
                                                <input class="btn btn-success" type="submit" value="Выдать">
                                            </form>
                                        @endif
                                        <p>{{$mg[0]->hasRole('master')?'Ремонты: ':'Встречи: '}} {{$mg_day['repairs']}}</p>
                                    </div>
                                </th>

                            @endif
                        @endforeach
                        <th class="fw-normal p-3 text-center" scope="col">
                            <div class="d-flex gap-3 flex-column">
                                <p>{{$mg[2]}}</p>
                                <form method="post" action="{{route('director.gsm.payall')}}">
                                    @csrf
                                    @method('post')
                                    <input type="hidden" name="manager" value={{$mg[0]->id}}>
                                    <input type="hidden" name="start_date" value={{$mg[1][0]['date']}}>
                                    <input type="hidden" name="end_date" value={{$mg[1][5]['date']}}>
                                    <input name="role" type="hidden" value="{{$mg[0]->hasRole('manager')?'manager':'master'}}">
                                    <input class="btn btn-primary" {{$mg[3]?'disabled':''}} type="submit" value="{{$mg[3]?'Весь ГСМ выдан':'Выдать неделю'}}">
                                </form>
                            </div>
                        </th>
                    </tr>
                @endforeach
                </tbody>

            </table>


        </div>


    </div>
@endsection
