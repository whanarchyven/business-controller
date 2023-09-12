@extends('layouts.app')

@section('title')
    {{'- Бонусы'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.bonuses').'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.bonuses').'?date='.$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        <div class="d-grid mt-5 grid">
            <div class="row row-cols-2">
                <div class="d-flex col flex-column">
                    <p class="fw-bold fs-3">Бонус за 15 000</p>
                    <table class="table table-bordered table-sm table-secondary ">
                        <thead class="">
                        <tr>
                            <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                            <th class="fw-bold p-2 text-left" scope="col">Бонус</th>
                            <th class="fw-bold p-2 text-left" scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bonuses15k as $bonus)
                            <tr>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->user->name}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->amount}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    @if(!$bonus->isPayed)
                                        <form method="post" action="{{route('director.bonuses.pay',$bonus)}}"
                                              class="d-flex w-auto">
                                            @csrf
                                            @method('patch')
                                            <input type="submit" value="Выплатить" class="btn w-100 btn-primary">
                                        </form>
                                    @else
                                        <button class="btn w-100 btn-success">Выплачено</button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="d-flex col flex-column">
                    <p class="fw-bold fs-3">Бонус за 50 000</p>
                    <table class="table table-bordered table-sm table-secondary ">
                        <thead class="">
                        <tr>
                            <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                            <th class="fw-bold p-2 text-left" scope="col">Бонус</th>
                            <th class="fw-bold p-2 text-left" scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bonuses50k as $bonus)
                            <tr>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->user->name}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->amount}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    @if(!$bonus->isPayed)
                                        <form method="post" action="{{route('director.bonuses.pay',$bonus)}}"
                                              class="d-flex w-auto">
                                            @csrf
                                            @method('patch')
                                            <input type="submit" value="Выплатить" class="btn w-100 btn-primary">
                                        </form>
                                    @else
                                        <button class="btn w-100 btn-success">Выплачено</button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="d-flex w-50 flex-column">
                    <p class="fw-bold fs-3">Бонус за 100 000</p>
                    <table class="table table-bordered table-sm table-secondary ">
                        <thead class="">
                        <tr>
                            <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                            <th class="fw-bold p-2 text-left" scope="col">Бонус</th>
                            <th class="fw-bold p-2 text-left" scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bonuses100k as $bonus)
                            <tr>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->user->name}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->amount}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    @if(!$bonus->isPayed)
                                        <form method="post" action="{{route('director.bonuses.pay',$bonus)}}"
                                              class="d-flex w-auto">
                                            @csrf
                                            @method('patch')
                                            <input type="submit" value="Выплатить" class="btn w-100 btn-primary">
                                        </form>
                                    @else
                                        <button class="btn w-100 btn-success">Выплачено</button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="d-flex w-50 flex-column">
                    <p class="fw-bold fs-3">Прочие бонусы</p>
                    <table class="table table-bordered table-sm table-secondary ">
                        <thead class="">
                        <tr>
                            <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                            <th class="fw-bold p-2 text-left" scope="col">Бонус</th>
                            <th class="fw-bold p-2 text-left" scope="col">Основание</th>
                            <th class="fw-bold p-2 text-left" scope="col"></th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($otherBonuses as $bonus)
                            <tr>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->user->name}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->amount}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    {{$bonus->reason}}
                                </th>
                                <th class="fw-normal p-2 text-left" scope="col">
                                    @if(!$bonus->isPayed)
                                        <form method="post" action="{{route('director.bonuses.pay',$bonus)}}"
                                              class="d-flex w-auto">
                                            @csrf
                                            @method('patch')
                                            <input type="submit" value="Выплатить" class="btn w-100 btn-primary">
                                        </form>
                                    @else
                                        <button class="btn w-100 btn-success">Выплачено</button>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
