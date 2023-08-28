@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header fs-1 d-flex justify-content-center">Ремонт от {{$repair->repair_date}}
                        на {{$repair->lead->issued}}/{{$repair->lead->avance}}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @role('director')
                        <form enctype="multipart/form-data"
                              action="{{route('repairs.leads.update',$repair)}}" method="post">
                            @csrf
                            @method('patch')
                            <div class="form-row d-flex gap-5 row-gap-10">
                                <div class="form-group w-50 my-2">
                                    <div class="form-group my-2">
                                        <label for="meeting_date">Дата продажи</label>
                                        <input value="{{$repair->lead->meeting_date}}" type="date" class="form-control"
                                               id='meeting_date' name="meeting_date"
                                               list="meeting_date">
                                    </div>
                                    <label for="status">Статус</label>
                                    <select id="status" class="form-control" name="status"
                                    >
                                        <option {{$repair->status=='in-work'?'selected':''}} value="in-work">В работе
                                        </option>
                                        <option {{$repair->status=='declined'?'selected':''}} value="declined">Отказано
                                        </option>
                                        <option {{$repair->status=='completed'?'selected':''}} value="completed">
                                            Выполнено
                                        </option>
                                    </select>
                                    <div class="form-group my-2">
                                        <label for="repair_date">Дата ремонта</label>
                                        <input value="{{$repair->repair_date}}" type="date" class="form-control"
                                               id='repair_date' name="repair_date"
                                               list="repair_date">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="contract_number">Номер договора</label>
                                        <input value="{{$repair->contract_number}}" type="text" class="form-control"
                                               id='contract_number' name="contract_number">
                                    </div>
                                    <label for="city">Город:</label>
                                    <input type="text" value="{{$repair->lead->city}}" class="form-control"
                                           id='city-input'
                                           name="city" list="city" onchange="updateSubcityOptions()">
                                    <datalist id="city">
                                        <option value="" disabled selected>Выберите город</option>
                                        {{--                            <option value="Москва">Москва</option>--}}
                                        {{--                            <option value="Санкт-Петербург">Санкт-Петербург</option>--}}
                                        {{--                            <option value="Новосибирск">Новосибирск</option>--}}
                                        {{--                            <option value="Екатеринбург">Екатеринбург</option>--}}
                                        {{--                            <option value="Нижний Новгород">Нижний Новгород</option>--}}
                                        {{--                            <option value="Казань">Казань</option>--}}
                                        {{--                            <option value="Челябинск">Челябинск</option>--}}
                                        {{--                            <option value="Омск">Омск</option>--}}
                                        {{--                            <option value="Самара">Самара</option>--}}
                                        {{--                            <option value="Ростов-на-Дону">Ростов-на-Дону</option>--}}
                                        @foreach($cities as $city)
                                            <option class="" value="{{$city->name}}"></option>
                                        @endforeach
                                    </datalist>
                                    <div class="form-group my-2">
                                        <label for="subcity">Подгород</label>
                                        <select id="subcity" value="{{$repair->lead->subcity}}" class="form-control"
                                                name="subcity"></select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="address">Адрес</label>
                                        <input type="text" value="{{$repair->lead->address}}" class="form-control"
                                               id='address'
                                               name="address" list="address">
                                    </div>

                                    <div class="form-group my-2">
                                        <label for="client_fullname">ФИО клиента</label>
                                        <input value="{{$repair->lead->client_fullname}}" type="text"
                                               class="form-control"
                                               id='client_fullname' name="client_fullname"
                                               list="client_fullname">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="phone">Номер телефона</label>
                                        <input type="tel" class="form-control" id='phone' name="phone"
                                               value="{{$repair->lead->phone}}" list="phone">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="job_type">Тип работ</label>
                                        <select id="job_type" class="form-control" name="job_type"
                                        >
                                            <option {{$repair->lead->job_type=="1"?'selected':''}} value="1">Окна
                                            </option>
                                            <option {{$repair->lead->job_type=="2"?'selected':''}} value="2">Конструкции
                                                ПВХ
                                            </option>
                                            <option {{$repair->lead->job_type=="3"?'selected':''}} value="3">
                                                Многопрофиль
                                            </option>
                                            <option {{$repair->lead->job_type=="4"?'selected':''}} value="4">Электрика
                                            </option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group w-50 my-2">
                                    <div class="form-group my-2">
                                        <label for="issued">Сумма</label>
                                        <input value="{{$repair->lead->issued}}" type="number" class="form-control"
                                               id='issued' name="issued"
                                               list="issued">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="avance">Предоплата</label>
                                        <input value="{{$repair->lead->avance}}" type="number" class="form-control"
                                               id='avance' name="avance"
                                               list="avance">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="manager_id">Менеджер</label>
                                        <select class="form-select" name="manager_id">
                                            @foreach($managers as $manager)
                                                <option
                                                    {{$manager->id==$repair->lead->manager_id?'selected':''}} value="{{$manager->id}}">{{$manager->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="master_id">Мастер</label>
                                        <select class="form-select" name="master_id">
                                            <option value="{{null}}">----Без мастера----</option>
                                            @foreach($masters as $master)
                                                <option
                                                    {{$master->id==$repair->master_id?'selected':''}} value="{{$master->id}}">{{$master->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="works">Список работ</label>
                                        <textarea name="works" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="note">Примечание</label>
                                        <textarea rows="3" type="text" class="form-control" id='note' name="note"
                                                  list="note">{{$repair->lead->note}}</textarea>
                                    </div>
                                    <label for="documents">Документы</label>
                                    <input value="{{$repair->lead->documents}}" enctype="multipart/form-data"
                                           type="file"
                                           class="my-2 form-control"
                                           name="documents[]"
                                           placeholder="Документы" multiple>
                                    <div class="d-flex  p-2  gap-3">
                                        @foreach($documents as $document)
                                            <a href="{{ URL::to('/documents') }}/{{$document}}"
                                               class="w-25 border border-2 border-black d-flex"><img
                                                    class="w-100 object-fit-cover"
                                                    src="{{ URL::to('/documents') }}/{{$document}}"/></a>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                            <div class="form-group my-2">
                                <input type="submit" class="form-control bg-primary text-white fw-bold"
                                       value="Отправить">
                            </div>

                        </form>
                        @endrole


                        <script>
                            const subcityData = {
                                "Москва": ["", "Люберцы", "Реутов", "Домодедово"],
                                "Санкт-Петербург": ["", "Пушкин", "Колпино", "Петергоф"],
                                "Новосибирск": ["", "Бердск", "Искитим", "Обь"],
                                "Екатеринбург": ["", "Лесной", "Верхняя Пышма", "Ревда"],
                                "Нижний Новгород": ["", "Бор", "Дзержинск", "Саров"],
                                "Казань": ["", "Набережные Челны", "Зеленодольск", "Елабуга"],
                                "Челябинск": ["", "Магнитогорск", "Златоуст", "Копейск"],
                                "Омск": ["", "Тара", "Исилькуль", "Калачинск"],
                                "Самара": ["", "Тольятти", "Новокуйбышевск", "Жигулевск"],
                                "Ростов-на-Дону": ["", "Шахты", "Батайск", "Новочеркасск"],
                                // Добавьте здесь другие города и адреса
                            };

                            function updateSubcityOptions() {
                                const citySelect = document.getElementById("city-input");
                                console.log(citySelect);
                                const subcitySelect = document.getElementById("subcity");
                                const selectedCity = citySelect.value;

                                // Очищаем список адресов
                                subcitySelect.innerHTML = "";

                                if (selectedCity) {
                                    const subcityes = subcityData[selectedCity];
                                    if (subcityes) {
                                        // Заполняем список адресов для выбранного города
                                        subcityes.forEach((subcity) => {
                                            const option = document.createElement("option");
                                            option.value = subcity;
                                            option.text = subcity;
                                            subcitySelect.appendChild(option);
                                        });
                                    }
                                }
                            }

                            document.addEventListener('DOMContentLoaded', updateSubcityOptions)
                        </script>
                    </div>
                </div>
            </div>
        </div>
@endsection
