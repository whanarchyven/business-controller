@extends('layouts.app')

@section('title')
    {{'- Создание заявки'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Новая заявка</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{route('leads.store')}}" method="post">
                            @csrf
                            <div class="form-row row-gap-10">
                                <div class="form-group my-2">
                                    <label for="city">Город:</label>
                                    <input type="text" class="form-control" id='city-input' name="city" list="city"
                                           onchange="updateSubcityOptions()">
                                    <datalist {{!$user->isAdmin?`disabled`:''}} id="city">
                                        @if($user->isAdmin)
                                            @foreach($cities as $city)
                                                <option class="" value="{{$city->name}}"></option>
                                            @endforeach
                                        @else
                                            @foreach($cities as $city)
                                                @if($city->id==$user->city)
                                                    <option class="" selected value="{{$city->name}}"></option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </datalist>
                                </div>
                                <div class="form-group my-2">
                                    <label for="subcity">Подгород</label>
                                    <select id="subcity" class="form-control" name="subcity"></select>
                                </div>
                                <div class="form-group my-2">
                                    <label for="address">Адрес</label>
                                    <input type="text" class="form-control" id='address' name="address" list="address">
                                </div>
                                {{--                                <div class="form-group my-2">--}}
                                {{--                                    <label for="meeting_date">Дата встречи</label>--}}
                                {{--                                    <input type="date" class="form-control" id='meeting_date' name="meeting_date"--}}
                                {{--                                           list="meeting_date">--}}
                                {{--                                </div>--}}
                                <div class="form-group my-2">
                                    <label for="time_period">Ожидает в </label>
                                    <select id="time_period" class="form-control" name="time_period">
                                        <option value="10-12">10-12</option>
                                        <option value="12-14">12-14</option>
                                        <option value="14-16">14-16</option>
                                        <option value="16-18">16-18</option>
                                        <option value="18-20">18-20</option>
                                    </select>
                                </div>
                                <div class="form-group my-2">
                                    <label for="client_fullname">ФИО клиента</label>
                                    <input type="text" class="form-control" id='client_fullname' name="client_fullname"
                                           list="client_fullname">
                                </div>
                                <div class="form-group my-2">
                                    <label for="phone">Номер телефона</label>
                                    <input type="tel" class="form-control" id='phone' name="phone" list="phone">
                                </div>
                                <div class="form-group my-2">
                                    <label for="job_type">Тип работ</label>
                                    <select id="job_type" class="form-control" name="job_type">
                                        <option value="1">Окна</option>
                                        <option value="2">Конструкции ПВХ</option>
                                        <option value="3">Многопрофиль</option>
                                        <option value="4">Электрика</option>
                                    </select>
                                </div>
                                <div class="form-group my-2">
                                    <label for="comment">Комментарий</label>
                                    <textarea rows="3" type="text" class="form-control" id='comment' name="comment"
                                              list="comment"></textarea>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="range" type="checkbox" id="range">
                                    <label class="form-check-label" for="range">
                                        Диапазон
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="measuring" type="checkbox" id="measuring">
                                    <label class="form-check-label" for="measuring">
                                        Замер
                                    </label>
                                </div>
                                <div class="form-group my-2">
                                    <label for="note">Примечание</label>
                                    <textarea rows="3" type="text" class="form-control" id='note' name="note"
                                              list="note"></textarea>
                                </div>
                                <div class="form-group my-2">
                                    <input type="submit" class="form-control bg-primary text-white fw-bold"
                                           value="Отправить">
                                </div>
                            </div>
                        </form>

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
                        </script>
                    </div>
                </div>
            </div>
        </div>
@endsection
