@extends('layouts.app')

@section('title')
    {{'- Удержания'}}
@endsection

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between">
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.deductions').'?date='.$prevMonthLink}}">Предыдущий
                    месяц</a>
            </div>
            <div id="date-head">
                <p class="fs-3">{{$dateTitle}}</p>
            </div>
            <div>
                <a class="bg-secondary px-4 rounded-2 py-2 text-white"
                   href="{{route('director.deductions').'?date='.$nextMonthLink}}">Следующий
                    месяц</a>
            </div>
        </div>
        <div class="d-flex col flex-column">
            <p class="fw-bold fs-3">Удержания</p>
            <table class="table table-bordered table-sm table-secondary ">
                <thead class="">
                <tr>
                    <th class="fw-bold p-2 text-left" scope="col">ФИО</th>
                    <th class="fw-bold p-2 text-left" scope="col">Удержание</th>
                    <th class="fw-bold p-2 text-left" scope="col">Основание</th>
                    <th class="fw-bold p-2 text-left" scope="col"></th>

                </tr>
                </thead>
                <tbody>
                @foreach($deductions as $deduction)
                    <tr>
                        <th class="fw-normal p-2 text-left" scope="col">
                            {{$deduction->user->name}}
                        </th>
                        <th class="fw-normal p-2 text-left" scope="col">
                            {{$deduction->amount}}
                        </th>
                        <th class="fw-normal p-2 text-left" scope="col">
                            {{$deduction->reason}}
                        </th>
                        <th class="fw-normal p-2 text-left" scope="col">
                            <form method="post" action="{{route('director.deductions.delete',$deduction)}}"
                                  class="d-flex w-auto">
                                @csrf
                                @method('delete')
                                <input type="submit" value="Удалить" class="btn w-100 btn-danger">
                            </form>
                        </th>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
@endsection
