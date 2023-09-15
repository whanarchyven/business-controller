@extends('layouts.app')

@section('title')
    {{'- Транзакции'}}
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
            <div class="bd-cyan-500">
                <div class="d-flex flex-row gap-3">
                    <p class="fs-3 text-indigo">Транзакции за {{$dateTitle}}, {{$city->name}}</p>
                    <button onclick="window.location.href='{{route('director.transactions.new')}}'"
                            class="btn btn-primary h-50">
                        Новая транзакция
                    </button>
                </div>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="p-2 fw-bold text-left" scope="col">Дата</th>
                        <th class="p-2 fw-bold text-left" scope="col">Статья</th>
                        <th class="p-2 fw-bold text-left" scope="col">Основание</th>
                        <th class="p-2 fw-bold text-left" scope="col">Приход/расход</th>
                        <th class="p-2 fw-bold text-left" scope="col">Баланс</th>
                        <th class="p-2 fw-bold text-left" scope="col">Ответственный</th>
                        <th class="p-2 fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <th class="p-2 fw-bold text-left" scope="col">
                                {{$transaction->created_at}}
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                {{$transaction->state->code. $transaction->state->name}}
                            </th>
                            <th class="p-2 fw-normal text-left" scope="col">
                                {{$transaction->description}}
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <p class="{{$transaction->type=='receipt'?'text-success':'text-danger'}}">
                                    {{$transaction->type=='receipt'?'+ '.$transaction->value:'- '.$transaction->value}}
                                </p>
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                {{$transaction->balance_stamp}}
                            </th>
                            <th class="fw-bold text-left" scope="col">
                                {{$transaction->user->name}}
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <button
                                    onclick="window.location.href='{{route('director.transactions.docs',$transaction)}}'"
                                    class="btn-warning btn">
                                    Просмотреть
                                </button>
                            </th>

                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
