@extends('layouts.app')

@section('title')
    {{'- Активные заявки'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <p class="fs-3">Привет, {{$manager->name}}</p>
        </div>

        <div class="">
            <div class="">
                <table class="table table-bordered table-sm table-secondary">
                    <thead class="table-light">
                    <tr class="bg-light">
                        <th class="fw-bold text-left" scope="col">Ожидает</th>
                        <th class="fw-bold text-left" scope="col">Инфо о клиенте</th>
                        <th class="fw-bold text-left" scope="col">Примечание</th>
                        <th class="fw-bold text-left" scope="col">Принял</th>
                        <th class="fw-bold text-left" scope="col">Вошел</th>
                        <th class="fw-bold text-left" scope="col">Вышел</th>
                        <th class="fw-bold text-left" scope="col">Сумма</th>
                        <th class="fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leads as $lead)
                        <tr class="p-3">
                            <th class="{{$lead->status=='declined'?"bg-declined":""}}  p-2 fw-bold text-left"
                                scope="col">
                                c {{preg_split("/[^1234567890]/", $lead->time_period)[0]}}
                                до {{preg_split("/[^1234567890]/", $lead->time_period)[1]}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <p class="mb-0 fw-normal"><strong>ФИО: </strong>{{$lead->client_fullname}}</p>
                                <p class="mb-0 fw-normal"><strong>Адрес: </strong>{{$lead->address}}</p>
                                <p class="mb-0 fw-normal"><strong>Телефон: </strong> {{$lead->phone}}</p>
                                <p class="mb-0 fw-normal"><strong>Доп: </strong>{{$lead->comment}}</p>
                                <p class="mb-0 fw-normal"><strong>Тип работ:</strong> {{$lead->jobType->name}}</p>
                            </th>
                            <th class="p-2 fw-normal text-left" scope="col">
                                {{$lead->note}}
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$lead->accepted}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$lead->entered}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$lead->exited}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$lead->check}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                @if($lead->status!='declined')
                                    @if(!$lead->accepted)
                                        <form action="{{route('leads.status',$lead->id)}}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input class="" name="status" type="hidden"
                                                   value="accepted"/>
                                            <input type="submit" class="my-2 btn btn-success w-100" value="Принял">
                                        </form>

                                    @elseif(!$lead->entered)
                                        <form id="accept" action="{{route('leads.status',$lead->id)}}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input class="" name="status" type="hidden"
                                                   value="entered"/>
                                            <input type="submit" class="btn btn-success w-100" value="Вошёл">
                                        </form>
                                        {{--                                        <form id="decline-form" class="d-none"--}}
                                        {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                        {{--                                              method="post">--}}
                                        {{--                                            @csrf--}}
                                        {{--                                            @method('PATCH')--}}
                                        {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                        {{--                                                   type="text"/>--}}
                                        {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                        {{--                                        </form>--}}
                                        {{--                                        <button id="decline" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                    @elseif(!$lead->exited)
                                        <form action="{{route('leads.status',$lead->id)}}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input class="" name="status" type="hidden"
                                                   value="exited"/>
                                            <input type="submit" class="btn btn-success w-100" value="Вышел">
                                        </form>
                                    @elseif(!$lead->check)
                                        {{--                                        <button id="success" class="my-2 btn btn-success w-100">Успешно</button>--}}
                                        {{--                                        <button id="otkaz" class="my-2 btn btn-danger w-100">Отказ</button>--}}
                                        {{--                                        <form id="decline-form-second" class="d-none"--}}
                                        {{--                                              action="{{route('leads.decline',$lead->id)}}"--}}
                                        {{--                                              method="post">--}}
                                        {{--                                            @csrf--}}
                                        {{--                                            @method('PATCH')--}}
                                        {{--                                            <input placeholder="Укажите причину отказа" class="form-control" name="note"--}}
                                        {{--                                                   type="text"/>--}}
                                        {{--                                            <input type="submit" class="my-2 btn btn-danger w-100" value="Отказ">--}}
                                        {{--                                        </form>--}}
                                        <form id="success-form" class="flex d-flex flex-column"
                                              enctype="multipart/form-data"
                                              action="{{route('leads.close',$lead->id)}}"
                                              method="post">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group d-flex flex-column">
                                                <label for="check">Сумма</label>
                                                <input class="form-control w-100 my-2" name="check" type="number"/>
                                                {{--                                                <label for="note">Примечание</label>--}}
                                                {{--                                                <textarea class="form-control w-100 my-2" name="note"--}}
                                                {{--                                                          type="text"></textarea>--}}
                                                {{--                                                                                                <label for="documents">Документы</label>--}}
                                                {{--                                                                                                <input enctype="multipart/form-data" type="file"--}}
                                                {{--                                                                                                       class="my-2 form-control"--}}
                                                {{--                                                                                                       name="documents[]"--}}
                                                {{--                                                                                                       placeholder="Документы" multiple>--}}
                                                <input type="submit" class="btn btn-success w-auto"
                                                       value="Закрыть встречу">
                                            </div>
                                        </form>
                                    @endif
                                @endif
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

        </div>
        <script>
            if (document.getElementById('decline')) {
                document.getElementById('decline').addEventListener(('click'), () => {
                    document.getElementById('decline').className = 'd-none';
                    document.getElementById('accept').className = 'd-none';
                    document.getElementById('decline-form').className = "d-flex flex-column";
                });
            }

            if (document.getElementById('otkaz')) {
                document.getElementById('otkaz').addEventListener(('click'), () => {
                    document.getElementById('otkaz').className = 'd-none';
                    document.getElementById('success').className = 'd-none';
                    document.getElementById('decline-form-second').className = "d-flex flex-column";
                });
            }

            if (document.getElementById('success')) {
                document.getElementById('success').addEventListener(('click'), () => {
                    document.getElementById('otkaz').className = 'd-none';
                    document.getElementById('success').className = 'd-none';
                    document.getElementById('success-form').className = "d-flex flex-column";
                });
            }
        </script>

    </div>
@endsection
