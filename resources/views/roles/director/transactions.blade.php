@extends('layouts.app')

@section('title')
    {{'- Транзакции'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.transactions')}}?date={{$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.transactions')}}?date={{$nextMonthLink}}">Следующий
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
                <a id="top"></a>
                <a href="#top" style="width: 60px; height: 60px; border-radius: 9999px; right: 20px; bottom: 60px" class="d-flex p-3 justify-content-center align-items-center position-fixed bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="#FFFFFF" class="bi bi-arrow-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"></path>
                    </svg>
                </a>
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
                <div class="w-100 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @foreach($paginationLinks as $link)
                                <li class="page-item {{$link==$currentPage?'active':''}} "><a class="page-link" href="{{$path.'?date='.$date.'&page='.$link}}">{{$link}}</a></li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>

    </div>
@endsection
