@extends('layouts.app')

@section('title')
    {{$title}}
@endsection

@section('content')
    <div class="container">
        {{--        @if($director->isAdmin)--}}
        {{--            @foreach($cities as $town)--}}
        {{--                <button--}}
        {{--                    class="btn {{$town->id==$city->id?'btn-secondary':'btn-outline-secondary'}} "--}}
        {{--                    onclick="window.location.href='/director/employers/{{$link}}/?city={{$town->id}}'">{{$town->name}}--}}
        {{--                </button>--}}
        {{--            @endforeach--}}
        {{--        @else--}}
        {{--            @foreach($cities as $town)--}}
        {{--                @if($town->id==$director->city)--}}
        {{--                    <button--}}
        {{--                        class="btn {{$town->id==$city->id?'btn-secondary':'btn-outline-secondary'}} "--}}
        {{--                        onclick="window.location.href='/director/employers/{{$link}}/?city={{$town->id}}'">{{$town->name}}--}}
        {{--                    </button>--}}
        {{--                @endif--}}
        {{--            @endforeach--}}
        {{--        @endif--}}
        <p class="fw-bold my-3 fs-3">{{$title}} - {{$city->name}}</p>
        <table class="table  table-bordered table-sm table-secondary">
            <thead class="table-light">
            <tr class="bg-light">
                <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                <th class="fw-bold p-2 text-left" scope="col">Дата рождения</th>
                <th class="fw-bold p-2 text-left" scope="col">Дата приёма</th>
                <th class="fw-bold p-2 text-left" scope="col">Телефон</th>
                <th class="fw-bold p-2 text-left" scope="col">Должность</th>
                <th class="fw-bold p-2 text-left" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="bg-light">
                    <th class="fw-normal p-2 text-left" scope="col">{{$user->name}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$user->birth_date}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$user->created_at}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$user->phone}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$role}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">
                        <div class="d-flex gap-3">
                            <form action="{{route('director.employers.delete',$user)}}" method="post">
                                @csrf
                                @method('delete')
                                <input type="hidden" value="dismissed">
                                <input class="btn btn-danger" type="submit" value="Уволить">
                            </form>
                            <button onclick="window.location.href='{{route('director.employers.edit',$user->id)}}'"
                                    class="btn btn-primary">
                                Редактировать
                            </button>
                            @if($user->hasRole('manager'))
                                <button onclick="window.location.href='{{route($route_card,$user)}}'"
                                        class="btn btn-warning">
                                    Карточка
                                </button>
                            @elseif($user->hasRole('operator'))
                                <button onclick="window.location.href='{{route($route_card,$user)}}'"
                                        class="btn btn-warning">
                                    Карточка
                                </button>
                            @endif
                        </div>
                    </th>
                </tr>
            @endforeach
            </tbody>

        </table>

        <p class="fw-bold my-3 fs-3">Уволенные</p>
        <table class="table  table-bordered table-sm table-secondary">
            <thead class="table-light">
            <tr class="bg-light">
                <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                <th class="fw-bold p-2 text-left" scope="col">Дата рождения</th>
                <th class="fw-bold p-2 text-left" scope="col">Дата приёма</th>
                <th class="fw-bold p-2 text-left" scope="col">Уволен</th>
                <th class="fw-bold p-2 text-left" scope="col">Телефон</th>
                <th class="fw-bold p-2 text-left" scope="col">Должность</th>
                <th class="fw-bold p-2 text-left" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($dismissed as $dis)
                <tr class="bg-light">
                    <th class="fw-normal p-2 text-left" scope="col">{{$dis->name}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$dis->birth_date}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$dis->created_at}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$dis->deleted_at}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$dis->phone}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">{{$role}}</th>
                    <th class="fw-normal p-2 text-left" scope="col">
                        <div class="d-flex gap-3">
                            <form action="{{route('director.employers.restore',$dis)}}" method="post">
                                @csrf
                                @method('post')
                                <input type="hidden" value="{{$dis->id}}" name="user">
                                <input class="btn btn-success" type="submit" value="Восстановить">
                            </form>
                        </div>
                    </th>
                </tr>
            @endforeach
            </tbody>

        </table>

    </div>
@endsection
