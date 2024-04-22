@extends('layouts.app')

@section('title')
    {{'- Новый сотрудник'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card">
                    <div class="card-header">Новый пользователь</div>
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form enctype="multipart/form-data" action="{{route('director.employers.store')}}"
                              method="post">
                            @csrf
                            <div class="form-row row-gap-10">
                                <div class="form-group my-2">
                                    <div class="form-group my-2">
                                        <label for="email">Логин</label>
                                        <input required type="text" class="form-control" id='email'
                                               name="email">
                                    </div>
                                    {{--                                    <div class="form-group my-2">--}}
                                    {{--                                        <label for="password">Пароль</label>--}}
                                    {{--                                        <input type="password" class="form-control" id='password'--}}
                                    {{--                                               name="password">--}}
                                    {{--                                    </div>--}}
                                    <div class="form-group my-2">
                                        <label for="name">ФИО</label>
                                        <input required type="text" class="form-control" id='name'
                                               name="name"
                                               list="name">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="birth_date">Дата рождения</label>
                                        <input required type="date" class="form-control" id='birth_date' name="birth_date"
                                               list="birth_date">
                                    </div>


                                    <div class="form-group my-2">
                                        <label for="phone">Номер телефона</label>
                                        <input required type="tel" class="form-control" id='phone' name="phone" list="phone">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="role">Должность</label>
                                        <select onchange="updateSubForm()" id="role" class="form-control" name="role">
                                            <option value="manager">Менеджер</option>
                                            <option value="master">Мастер</option>
                                            @if($director->isAdmin)
                                                <option value="coordinator">Координатор</option>
                                                <option value="operator">Оператор</option>
                                                <option value="director">Руководитель</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div id="teacher-form" class="form-group my-2 d-none">
                                        <label for="role">Ментор (наставник)</label>
                                        <select onchange="updateSubForm()" id="teacher_id" class="form-control" name="teacher_id">
                                            <option value={{-1}}>{{'БЕЗ НАСТАВНИКA'}}</option>
                                            @foreach($managers as $manager)
                                                <option value={{$manager->id}}>{{$manager->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="mentor-form" class="form-group my-2 d-none">
                                        <label for="role">Тимлидер</label>
                                        <select onchange="updateSubForm()" id="mentor_id" class="form-control" name="mentor_id">
                                            <option value={{-1}}>{{'БЕЗ ТИМЛИДА'}}</option>
                                            @foreach($managers as $manager)
                                                <option value={{$manager->id}}>{{$manager->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>



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
                                                    @if($city->id!=999)
                                                        <option value="{{$city->id}}">{{$city->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif


                                    @if($director->isAdmin)
                                        <div id="city-form-coordinator" class="my-2 d-none">
                                            <label for="city">Доступ к городам</label>
                                            @foreach($cities as $city)
                                                @if($city->id!=999)
                                                    <div class="d-flex gap-3">
                                                    <label>{{$city->name}}</label>
                                                    <input id="{{$city->name}}" name="{{$city->name}}" type="checkbox"/>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif


                                    {{--                                    <div id="oklad" class="d-none">--}}
                                    {{--                                        <label for="bet">Оклад (дневная ставка)</label>--}}
                                    {{--                                        <input type="tel" class="form-control" id='bet' name="bet" list="bet">--}}
                                    {{--                                    </div>--}}

                                    <label for="documents">Документы</label>
                                    <input enctype="multipart/form-data"
                                           type="file"
                                           class="my-2 form-control"
                                           name="documents[]"
                                           placeholder="Документы" multiple>

                                    <div class="form-group my-2">
                                        <input type="submit" class="form-control bg-primary text-white fw-bold"
                                               value="Сохранить">
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                            function updateSubForm() {
                                let select = document.querySelector("#role");
                                console.log(select.value);
                                if (select.value == 'coordinator') {
                                    document.getElementById('city-form').className = 'd-none';
                                    document.getElementById('mentor-form').className = 'd-none'
                                    document.getElementById('teacher-form').className = 'd-none'
                                    document.getElementById('city-form-coordinator').className = 'form-group my-2';
                                }
                                else if(select.value=='manager'){
                                    document.getElementById('city-form').className = 'form-group my-2';
                                    document.getElementById('city-form-coordinator').className = 'd-none';
                                    // document.getElementById('mentor-form').className = 'form-group my-2'
                                    document.getElementById('teacher-form').className = 'form-group my-2'
                                }
                                else {
                                    document.getElementById('city-form').className = 'form-group my-2';
                                    document.getElementById('city-form-coordinator').className = 'd-none';
                                    document.getElementById('mentor-form').className = 'd-none'
                                    document.getElementById('teacher-form').className = 'd-none'
                                }
                            }
                            document.querySelector("#role").addEventListener('select', updateSubForm)
                            updateSubForm();
                        </script>

                    </div>
                </div>
            </div>
        </div>
@endsection
