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
                                        <input type="text" class="form-control" id='email'
                                               name="email">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="password">Пароль</label>
                                        <input type="password" class="form-control" id='password'
                                               name="password">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="name">ФИО</label>
                                        <input type="text" class="form-control" id='name'
                                               name="name"
                                               list="name">
                                    </div>


                                    <div class="form-group my-2">
                                        <label for="phone">Номер телефона</label>
                                        <input type="tel" class="form-control" id='phone' name="phone" list="phone">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="role">Должность</label>
                                        <select onchange="updateSubForm()" id="role" class="form-control" name="role">
                                            <option value="operator">Оператор</option>
                                            <option value="manager">Менеджер</option>
                                            <option value="coordinator">Координатор</option>
                                        </select>
                                    </div>

                                    <div id="coordinator-form" class="d-none">
                                        <label for="coordinator_id">Закреплённый координатор</label>
                                        <select id="coordinator_id" class="form-control" name="coordinator_id">
                                            @foreach($coordinators as $coordinator)
                                                <option value="{{$coordinator->id}}">{{$coordinator->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group my-2">
                                        <label for="bet">Оклад (дневная ставка)</label>
                                        <input type="tel" class="form-control" id='bet' name="bet" list="bet">
                                    </div>

                                    <label for="documents">Документы</label>
                                    <input enctype="multipart/form-data"
                                           type="file"
                                           class="my-2 form-control"
                                           name="documents[]"
                                           placeholder="Документы" multiple>

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
                                    document.querySelector('#coordinator-form').className = 'form-group my-2'
                                } else {
                                    document.querySelector('#coordinator-form').className = 'd-none'
                                }
                            }

                            // document.querySelector("#role").addEventListener('select', updateSubForm)
                        </script>
                    </div>
                </div>
            </div>
        </div>
@endsection
