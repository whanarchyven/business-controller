@extends('layouts.app')

@section('title')
    {{'- Выдача материала'}}
@endsection

@section('content')
    <div class="container">

        <div class="">
            <div class="bd-cyan-500">
                <p class="fw-bold m-0 fs-2">Выдача материала</p>
                <p class="fw-normal m-0 fs-4">Активные ремонты</p>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="p-2 fw-bold text-left" scope="col">Дата</th>
                        <th class="p-2 fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="p-2 fw-bold text-left" scope="col"></th>
                        <th class="p-2 fw-bold text-left" scope="col">Примечание</th>
                        <th class="p-2 fw-bold text-left" scope="col">Мастер</th>
                        <th class="p-2 fw-bold text-left" scope="col">Редактировать</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($repairs as $repair)
                        <tr>
                            <th class="p-2 fw-bold text-left" scope="col">
                                {{$repair->repair_date}} <br/>
                                {{$repair->lead->city}}
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$repair->lead->client_fullname}}</p>
                                <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$repair->lead->address}}</p>
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>Телефон: </strong> <br/>{{$repair->lead->phone}}</p>
                                <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$repair->lead->comment}}</p>
                            </th>
                            <th class="p-2 fw-normal text-left" scope="col">{{$repair->lead->note}}</th>

                            <th class="p-2 fw-bold text-left" scope="col">
                                <p>{{$repair->master?$repair->master->shortname() :'Не назначено'}}</p>
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <button onclick="window.location.href='{{route('director.expense.new',$repair)}}'"
                                        class="btn btn-primary">Выдать материал
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
