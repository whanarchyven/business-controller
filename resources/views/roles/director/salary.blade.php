@extends('layouts.app')

@section('title')
    {{'- Ведомость сотрудники'}}
@endsection

@section('content')
    <div id="check" class="container">
        <div>
            <div class="d-flex justify-content-between">
                <div>
                    <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                       href="{{route('director.salary.pay').'?date='.$prevMonthLink}}">Предыдущий
                        месяц</a>
                </div>
                <div id="date-head">
                    <p class="fs-3">{{$dateTitle}}</p>
                </div>
                <div>
                    <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                       href="{{route('director.salary.pay').'?date='.$nextMonthLink}}">Следующий
                        месяц</a>
                </div>
            </div>

            <div class="mt-4">
                <p class="fw-bold fs-2">Руководитель</p>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>
                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>
                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>
                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>
                        <th class="p-2 fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($directors as $director)
                        @if(!$director->isAdmin)
                            <tr class="table-light">
                                <th class="p-2 fw-bold text-left" scope="col">{{$director->shortname()}}</th>
                                <th class="p-2 fw-bold text-left" scope="col">{{$director->deductions($date)}}</th>
                                <th class="p-2 fw-bold text-left" scope="col">{{$director->payedSalary($date)}}</th>
                                <th class="p-2 fw-bold text-left summ {{$director->salary($date)-$director->payedSalary($date)<0?'text-danger':'text-black'}}"
                                    scope="col">{{$director->salary($date)-$director->payedSalary($date)}}</th>
                                <th class=" p-2 fw-bold text-left" scope="col">
                                    @if($director->salary($date)-$director->payedSalary($date)>0)
                                        <form action="{{route('director.salary.payall',$director)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="data" value="{{$date}}"/>
                                            <input type="submit"
                                                   class="btn w-100 btn-success" value="Выплатить"/>
                                        </form>
                                    @endif

                                </th>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>

                </table>
            </div>

            <div class="mt-4">
                <p class="fw-bold fs-2">Менеджеры</p>
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>
                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>
                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>
                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>
                        <th class="p-2 fw-bold text-left" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($managers as $manager)
                        <tr class="table-light">
                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->shortname()}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->deductions($date)}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">{{$manager->payedSalary($date)}}</th>
                            <th class="p-2 fw-bold text-left summ {{$manager->salary($date)-$manager->payedSalary($date)<0?'text-danger':'text-black'}}"
                                scope="col">{{$manager->salary($date)-$manager->payedSalary($date)}}</th>

                            <th class="p-2 fw-bold text-left" scope="col">
                                @if($manager->salary($date)-$manager->payedSalary($date)>0)
                                    <form action="{{route('director.salary.payall',$manager)}}" method="post">
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="data" value="{{$date}}"/>
                                        <input type="submit"
                                               class="btn w-100 btn-success" value="Выплатить"/>
                                    </form>
                                @endif

                            </th>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>

{{--            <div class="mt-4">--}}
{{--                <p class="fw-bold fs-2">Операторы</p>--}}
{{--                <table class="table table-bordered table-sm table-secondary ">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>--}}
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
{{--                                @if($operator->salary($date)-$operator->payedSalary($date)>0)--}}
{{--                                    <form action="{{route('director.salary.payall',$operator)}}" method="post">--}}
{{--                                        @csrf--}}
{{--                                        @method('patch')--}}
{{--                                        <input type="hidden" name="data" value="{{$date}}"/>--}}
{{--                                        <input type="submit"--}}
{{--                                               class="btn w-100 btn-success" value="Выплатить"/>--}}
{{--                                    </form>--}}
{{--                                @endif--}}

{{--                            </th>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}

{{--                </table>--}}
{{--            </div>--}}

{{--            <div class="mt-4">--}}
{{--                <p class="fw-bold fs-2">Мастера</p>--}}
{{--                <table class="table table-bordered table-sm table-secondary ">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">ФИО</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Удержано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">Выдано</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col">К выдаче</th>--}}
{{--                        <th class="p-2 fw-bold text-left" scope="col"></th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($masters as $master)--}}
{{--                        <tr class="table-light">--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$master->name}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$master->deductions($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left" scope="col">{{$master->payedSalary($date)}}</th>--}}
{{--                            <th class="p-2 fw-bold text-left summ {{$master->salary($date)-$master->payedSalary($date)<0?'text-danger':'text-black'}}"--}}
{{--                                scope="col">{{$master->salary($date)-$master->payedSalary($date)}}</th>--}}

{{--                            <th class="p-2 fw-bold text-left" scope="col">--}}
{{--                                @if($master->salary($date)-$master->payedSalary($date)>0)--}}
{{--                                    <form action="{{route('director.salary.payall',$master)}}" method="post">--}}
{{--                                        @csrf--}}
{{--                                        @method('patch')--}}
{{--                                        <input type="hidden" name="data" value="{{$date}}"/>--}}
{{--                                        <input type="submit"--}}
{{--                                               class="btn w-100 btn-success" value="Выплатить"/>--}}
{{--                                    </form>--}}
{{--                                @endif--}}

{{--                            </th>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}

{{--                </table>--}}
{{--            </div>--}}
            <p class="fw-bold fs-2">Итого: <span id="total"></span></p>
            <div class="d-flex gap-3">
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
                    let inputsHTML = document.getElementsByClassName('summ')
                    let inputs = [...inputsHTML];
                    console.log(inputs);
                    let totalValue = 0;
                    inputs.map((item) => {
                        totalValue += Number(item.textContent)
                    })
                    console.log(totalValue);
                    document.getElementById('total').innerText = totalValue;
                }

                checkPay();
            </script>
        </div>
    </div>
@endsection
