<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Бизнес контроллер') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body onload="clockTimer();">

<script>
    function clockTimer() {
        var date = new Date();
        var time = [date.getHours(), date.getMinutes(), date.getSeconds()]; // |[0] = Hours| |[1] = Minutes| |[2] = Seconds|
        var days = date.getDay();
        if (time[0] < 10) {
            time[0] = "0" + time[0];
        }
        if (time[1] < 10) {
            time[1] = "0" + time[1];
        }
        if (time[2] < 10) {
            time[2] = "0" + time[2];
        }
        var current_time = [time[0], time[1], time[2]].join(':');
        var clock = document.getElementById("clock");
        clock.innerHTML = current_time;
        setTimeout("clockTimer()", 1000);
    }
</script>

<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <div class="clockpage">
                <span class="fw-bold fs-4" id="clock"></span>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @role('operator')
                <ul class="navbar-nav d-flex flex-row container align-items-center justify-content-around ">
                    <li class="nav-item mx-3">
                        <a class="dropdown-item" href="{{ route('leads.create') }}">
                            Создать заявку
                        </a>
                    </li>
                    <li class="nav-item mx-3 border border-dark p-2">
                        <a class="dropdown-item fw-bold" href="{{ route('card.operator') }}">
                            Карточка
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Заявки
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('leads.index')}}">
                                Оформленные заявки
                            </a>
                            <a class="dropdown-item"
                               href="{{route('leads.declined')}}">
                                Отклонённые заявки
                            </a>
                        </div>
                    </li>


                </ul>
                @endrole


                @role('manager')
                <ul class="navbar-nav d-flex flex-row container align-items-center justify-content-around ">
                    <li class="nav-item mx-3 border border-dark p-2">
                        <a class="dropdown-item fw-bold" href="{{ route('manager.card') }}">
                            Карточка
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="{{route('manager.leads')}}">
                            Заявки
                        </a>
                    </li>


                </ul>
                @endrole


                @role('coordinator')
                <ul class="navbar-nav d-flex flex-row container align-items-center justify-content-around ">
                    <li class="nav-item mx-3">
                        <a class="dropdown-item" href="{{ route('leads.create') }}">
                            Создать заявку
                        </a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="dropdown-item" href="{{ route('coordinator.managers') }}">
                            Таблица контроля
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Заявки
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('leads.index')}}">
                                Оформленные заявки
                            </a>
                            <a class="dropdown-item"
                               href="{{route('leads.declined')}}">
                                Отклонённые заявки
                            </a>
                        </div>
                    </li>


                </ul>
                @endrole

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}"></a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
</html>
