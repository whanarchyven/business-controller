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
                       href="/avance/week?date={{\Carbon\Carbon::createFromDate($date)->subWeek()->toDateString()}}&day={{$prevSaturday}}">Предыдущая
                        неделя</a>
                </div>
                <div id="date-head" {{\Carbon\Carbon::setLocale('ru')}}>
                    <p class="fs-3">{{\Illuminate\Support\Carbon::createFromDate($prevSaturday)->translatedFormat('j F Y')}} - {{\Illuminate\Support\Carbon::createFromDate($nextSaturday)->translatedFormat('j F Y')}}</p>
                </div>
                <div>
                    <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                       href="/avance/week?date={{\Carbon\Carbon::createFromDate($date)->addWeek()->toDateString()}}&day={{$nextSaturday}}">Следующая
                        неделя</a>
                </div>

            </div>


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
                            <th class="p-2 fw-bold text-left" scope="col">{{$master->masterWeekPayed($prevSaturday,$nextSaturday)}}</th>
                            <th class="p-2 fw-bold text-left summ {{$master->masterWeek($prevSaturday,$nextSaturday)-$master->masterWeekPayed($prevSaturday,$nextSaturday)<0?'text-danger':'text-black'}}"
                                scope="col">{{$master->masterWeek($prevSaturday,$nextSaturday)-$master->masterWeekPayed($prevSaturday,$nextSaturday)}}</th>
                            <th class="p-2 fw-bold text-left" scope="col">
                                <input onchange="checkPay()" required class="form-control"
                                       value="{{$master->masterWeek($prevSaturday,$nextSaturday)-$master->masterWeekPayed($prevSaturday,$nextSaturday)<5000?$master->masterWeek($prevSaturday,$nextSaturday)-$master->masterWeekPayed($prevSaturday,$nextSaturday):5000}}" max="{{$master->masterWeek($prevSaturday,$nextSaturday)-$master->masterWeekPayed($prevSaturday,$nextSaturday)}}" type="number"
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
