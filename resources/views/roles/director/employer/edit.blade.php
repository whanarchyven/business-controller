@extends('layouts.app')

@section('title')
    {{'Редактирование сотрудника'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Редактирование сотрудника</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form enctype="multipart/form-data" action="{{route('director.employers.update',$user)}}"
                              method="post">
                            @csrf
                            @method('patch')
                            <div class="form-row row-gap-10">
                                <div class="form-group my-2">
                                    <div class="form-group my-2">
                                        <label for="email">Логин</label>
                                        <input value="{{$user->email}}" type="text" class="form-control" id='email'
                                               name="email">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="password">Пароль</label>
                                        <input value="" type="password" class="form-control" id='password'
                                               name="password">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="name">ФИО</label>
                                        <input value="{{$user->name}}" type="text" class="form-control" id='name'
                                               name="name"
                                               list="name"/>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="birth_date">Дата рождения</label>
                                        <input value="{{$user->birth_date}}" type="date" class="form-control"
                                               id='birth_date' name="birth_date"
                                               list="birth_date">
                                    </div>


                                    <div class="form-group my-2">
                                        <label for="phone">Номер телефона</label>
                                        <input value="{{$user->phone}}" type="tel" class="form-control" id='phone'
                                               name="phone" list="phone">
                                    </div>
                                    {{--                                    <div class="form-group my-2">--}}
                                    {{--                                        <label for="role">Должность</label>--}}
                                    {{--                                        <select onchange="updateSubForm()" id="role" class="form-control" name="role">--}}
                                    {{--                                            <option value="coordinator">Координатор</option>--}}
                                    {{--                                            <option value="manager">Менеджер</option>--}}
                                    {{--                                            <option value="master">Мастер</option>--}}
                                    {{--                                            @if($director->isAdmin)--}}
                                    {{--                                                <option value="operator">Оператор</option>--}}
                                    {{--                                                <option value="director">Руководитель</option>--}}
                                    {{--                                            @endif--}}
                                    {{--                                        </select>--}}
                                    {{--                                    </div>--}}

                                    {{--                                    <div id="coordinator-form" class="d-none">--}}
                                    {{--                                        <label for="coordinator_id">Закреплённый координатор</label>--}}
                                    {{--                                        <select id="coordinator_id" class="form-control" name="coordinator_id">--}}
                                    {{--                                            @foreach($coordinators as $coordinator)--}}
                                    {{--                                                <option value="{{$coordinator->id}}">{{$coordinator->name}}</option>--}}
                                    {{--                                            @endforeach--}}
                                    {{--                                        </select>--}}
                                    {{--                                    </div>--}}

                                    @if($director->isAdmin)
                                        <div id="city-form" class="my-2">
                                            <label for="city">Город</label>
                                            <select id="city" class="form-control" name="city">
                                                @foreach($cities as $city)
                                                    <option
                                                        {{$city->id==$user->city?`selevted`:''}} value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if($user->hasRole('manager'))
                                        <div id="oklad" class="form-group my-2">
                                            <label for="bet">Оклад (дневная ставка)</label>
                                            <input type="tel" value="{{$user->bet}}" class="form-control" id='bet'
                                                   name="bet" list="bet">
                                        </div>
                                    @endif

                                    {{--                                    <label for="documents">Документы</label>--}}
                                    {{--                                    <input enctype="multipart/form-data"--}}
                                    {{--                                           type="file"--}}
                                    {{--                                           class="my-2 form-control"--}}
                                    {{--                                           name="documents[]"--}}
                                    {{--                                           placeholder="Документы" multiple>--}}

                                    <div class="form-group my-2">
                                        <input type="submit" class="form-control bg-primary text-white fw-bold"
                                               value="Отправить">
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                            function updateSubForm() {
                                let select = document.querySelector("#role");
                                console.log(select.value);
                                if (select.value == 'manager') {
                                    document.querySelector('#coordinator-form').className = 'form-group my-2';
                                    document.getElementById('oklad').className = 'form-group my-2';
                                } else if (select.value == 'coordinator') {
                                    document.querySelector('#coordinator-form').className = 'd-none'
                                    document.getElementById('oklad').className = 'form-group my-2';
                                } else {
                                    document.querySelector('#coordinator-form').className = 'd-none'
                                    document.getElementById('oklad').className = 'd-none';
                                }
                            }

                            // document.querySelector("#role").addEventListener('select', updateSubForm)
                        </script>
                    </div>
                </div>
            </div>
        </div>
@endsection
