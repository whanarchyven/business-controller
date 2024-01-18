@extends('layouts.app')

@section('title')
    {{'- Аванс неделя'}}
@endsection

@section('content')
    <div id="check" class="container">
        <form method="post" action="{{route('director.avance.pay')}}">
            @csrf
            @method('post')

            <div class="d-flex justify-content-between">
                <div>
                    <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                       href="{{route('director.avance.week').'?date='.$prevMonthLink}}">Предыдущий
                        месяц</a>
                </div>
                <div id="date-head">
                    <p class="fs-3">{{$dateTitle}}</p>
                </div>
                <div>
                    <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                       href="{{route('director.avance.week').'?date='.$nextMonthLink}}">Следующий
                        месяц</a>
                </div>
            </div>

{{--            <div class="mt-4">--}}
{{--                <p class="fw-bold fs-2">Руководитель</p>--}}
{{--                <table class="table table-bordered table-sm table-secondary ">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Аванс</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col"></th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($directors as $director)--}}
{{--                        @if(!$director->isAdmin)--}}
{{--                            <tr class="table-light">--}}
{{--                                <th class="p-2 fw-bold text-left" scope="col">{{$director->name}}</th>--}}
{{--                                <th class="p-2 fw-bold text-left" scope="col">{{$director->deductions($date)}}</th>--}}
{{--                                <th class="p-2 fw-bold text-left" scope="col">{{$director->payedSalary($date)}}</th>--}}
{{--                                <th class="p-2 fw-bold text-left" scope="col">{{$director->salary($date)}}</th>--}}
{{--                                <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                    <input class="form-control"--}}
{{--                                           value="5000"--}}
{{--                                           type="number"--}}
{{--                                           name="director{{$loop->index}}"--}}
{{--                                        --}}{{--                                       max="{{$director->salary($date)}}"--}}
{{--                                    />--}}
{{--                                    <input type="hidden" value="{{$director->id}}" name="directorEmployer{{$loop->index}}">--}}
{{--                                </th>--}}
{{--                                <th class=" p-2 fw-bold text-left" scope="col">--}}
{{--                                    <div onclick="window.location.href='{{route('director.directorcard',$director)}}'"--}}
{{--                                         class="btn w-100 btn-warning">--}}
{{--                                        Карточка--}}
{{--                                    </div>--}}
{{--                                </th>--}}
{{--                            </tr>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}

{{--                </table>--}}
{{--            </div>--}}

{{--            <div class="mt-4">--}}
{{--                <p class="fw-bold fs-2">Менеджеры</p>--}}
{{--                <table class="table table-bordered table-sm table-secondary ">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Аванс</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col"></th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($managers as $manager)--}}
{{--                        <tr class="table-light">--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->name}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->deductions($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->payedSalary($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->salary($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                <input class="form-control"--}}
{{--                                       value="5000"--}}
{{--                                       --}}{{--                                       max="{{$manager->salary($date)}}" type="number"--}}
{{--                                       name="manager{{$loop->index}}"/>--}}
{{--                                <input type="hidden" value="{{$manager->id}}" name="managerEmployer{{$loop->index}}">--}}
{{--                            </th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                <div onclick="window.location.href='{{route('director.managercard',$manager)}}'"--}}
{{--                                     class="btn w-100 btn-warning">--}}
{{--                                    Карточка--}}
{{--                                </div>--}}
{{--                            </th>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}

{{--                </table>--}}
{{--            </div>--}}

{{--            <div class="mt-4">--}}
{{--                <p class="fw-bold fs-2">Операторы</p>--}}
{{--                <table class="table table-bordered table-sm table-secondary ">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Аванс</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col"></th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($operators as $operator)--}}
{{--                        <tr class="table-light">--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$operator->name}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$operator->deductions($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$operator->payedSalary($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left summ {{$operator->salary($date)-$operator->payedSalary($date)<0?'text-danger':'text-black'}}"--}}
{{--                                scope="col">{{$operator->salary($date)-$operator->payedSalary($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                <input class="form-control" required--}}
{{--                                       value="5000" onchange="checkPay()"--}}
{{--                                       --}}{{--                                       max="{{$operator->salary($date)}}"--}}
{{--                                       type="number"--}}
{{--                                       name="operator{{$loop->index}}"/>--}}
{{--                                <input type="hidden" value="{{$operator->id}}" name="operatorEmployer{{$loop->index}}">--}}
{{--                            </th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                <div onclick="window.location.href='{{route('director.operatorcard',$operator)}}'"--}}
{{--                                     class="btn w-100 btn-warning">--}}
{{--                                    Карточка--}}
{{--                                </div>--}}
{{--                            </th>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}

{{--                </table>--}}
{{--            </div>--}}

            <div class="mt-4">
                <p class="fw-bold fs-2">Мастера</p>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>
                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>
                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>
                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>
                        <th class="p-2 fw-bold text-left" scope="col">Аванс</th>
                        <th class="p-2 fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($masters as $master)
                        <tr class="table-light">
                            <th class="p-2 fw-bold text-left" scope="col">{{$master->shortname()}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$master->deductions($date)}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$master->payedSalary($date)}}</th>
                            <th class="p-2 fw-bold text-left summ {{$master->salary($date)-$master->payedSalary($date)<0?'text-danger':'text-black'}}"
                                scope="col">{{$master->salary($date)-$master->payedSalary($date)}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <input onchange="checkPay()" required class="form-control"
                                       value="{{$master->salary($date)-$master->payedSalary($date)<5000?$master->salary($date)-$master->payedSalary($date):5000}}" max="{{$master->salary($date)-$master->payedSalary($date)}}" type="number"
                                       name="master{{$loop->index}}"/>
                                <input type="hidden" value="{{$master->id}}" name="masterEmployer{{$loop->index}}">
                            </th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <div onclick="window.location.href='{{route('director.mastercard',$master)}}'"
                                     class="btn w-100 btn-warning">
                                    Карточка
                                </div>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
            <p class="fw-bold fs-2">Итого: <span id="total"></span></p>
            <div class="d-flex gap-3">
                <input type="hidden" name="date" value="{{$date}}"/>
                <input type="submit" value="Выдать авансы" class="btn btn-danger"/>
                <div onclick="generatePDF()" class="btn btn-primary">Печать</div>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.js"></script>
            <script>
                function generatePDF() {
                    const element = document.getElementById('check');
                    html2pdf()
                        .from(element)
                        .save();
                }

                function checkPay() {
                    let inputsHTML = document.getElementsByClassName('form-control')
                    let inputs = [...inputsHTML];
                    console.log(inputs);
                    let totalValue = 0;
                    inputs.map((item) => {
                        totalValue += Number(item.value)
                    })
                    console.log(totalValue);
                    document.getElementById('total').innerText = totalValue;
                }

                checkPay();
            </script>
        </form>
    </div>
@endsection
