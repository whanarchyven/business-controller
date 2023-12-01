@extends('layouts.app')

@section('title')
    {{'- Транзакции'}}
@endsection

@section('content')
    <div class="container">

        <div class="">
            <div class="bd-cyan-500">
                <div class="d-flex flex-row gap-3">
                    <p class="fs-3 text-indigo">Поиск транзакций</p>
                </div>
                <a id="top"></a>
                <a href="#top" style="width: 60px; height: 60px; border-radius: 9999px; right: 20px; bottom: 150px" class="d-flex p-3 justify-content-center align-items-center position-fixed bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="#FFFFFF" class="bi bi-arrow-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"></path>
                    </svg>
                </a>
                <a href="#bottom" style="width: 60px; height: 60px; border-radius: 9999px; right: 20px; bottom: 60px;transform: rotate(180deg)" class="d-flex p-3 justify-content-center align-items-center position-fixed bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="#FFFFFF" class="bi bi-arrow-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"></path>
                    </svg>
                </a>
                <form method="post" action="{{route('director.transactions.do.search')}}" class="my-3 mb-6 d-grid gap-2">
                    @csrf
                    @method('post')
                    <div class="row">
                        <div class="d-flex col flex-column gap-2">
                            <p class="m-0">Дата</p>
                            <input class="form-control" type="date" value="{{$date?$date:''}}" name="date"/>
                        </div>
                        <div class="d-flex col flex-column gap-2">
                            <p class="m-0">Тип</p>
                            <select name="type" class="form-control">
                                <option value="">Не выбрано</option>
                                <option {{$type=='receipt'?'selected':''}} value="receipt">Приход</option>
                                <option {{$type=='expense'?'selected':''}} value="expense">Расход</option>
                            </select>
                        </div>
                        <div class="d-flex col flex-column gap-2">
                            <p class="m-0">Статья ID</p>
                            <input placeholder="{{$state?\App\Models\TransactionState::where(["id"=>$state])->first()->code.\App\Models\TransactionState::where(["id"=>$state])->first()->name:''}}" class="form-control" list="state-list" id="state" name="state" />
                            <datalist id="state-list">
                                @foreach($states as $stateTemp)
                                    <option value="{{$stateTemp->id}}">{{$stateTemp->code}} {{$stateTemp->name}}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex col flex-column gap-2">
                            <p class="m-0">Основание</p>
                            <input class="form-control" value="{{$description?$description:''}}" name="description"/>
                        </div>
                        <div class="d-flex col flex-column gap-2">
                            <p class="m-0">Ответственный ID</p>
                            <input placeholder="{{$responsible?\App\Models\User::where(["id"=>$responsible])->first()->name:''}}" class="form-control" list="responsible-list" id="responsible" name="responsible" />
                            <datalist id="responsible-list">
                                @foreach($allUsers as $user)
                                    @if($user->city==$city->id)
                                        <option data-text="{{$user->name}}" value="{{$user->id}}">{{$user->name}}</option>
                                    @endif
                                @endforeach
                            </datalist>
                        </div>

                    </div>

                    <div class="row ">
                        <input class="btn btn-primary" type="submit" value="Поиск" />
                    </div>
                    <div class="row ">
                        <div onclick="window.location='{{route('director.transactions.search')}}'" class="btn m-0 btn-danger">Сброс</div>
                    </div>

                </form>
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
                <a id="bottom"></a>
            </div>

        </div>

    </div>
@endsection
