@extends('layouts.app')

@section('content')
    <div class="container">


        <div class="">
            <div class="d-flex flex-row mb-3 align-items-center gap-3">
                <p class="fw-bold m-0 fs-2">Склад</p>
                <button onclick="window.location.href='{{route('director.add.nomenclature')}}'"
                        class="btn btn-primary h-25">Добавить номенклатуру
                </button>
            </div>
            <div class="bd-cyan-500">
                <table class="table table-bordered table-sm table-secondary ">
                    <thead>
                    <tr>
                        <th class="fw-bold text-left" scope="col">Название</th>
                        <th class="fw-bold text-left" scope="col">Единица измерения</th>
                        <th class="fw-bold text-left" scope="col">Остаток</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($nomenclature as $item)
                        <tr>
                            <th class="fw-normal text-left" scope="col">{{$item->name}}</th>
                            <th class="fw-normal text-left" scope="col">{{$item->unit}}</th>
                            <th class="fw-bold text-left" scope="col">{{$item->remain}}</th>
                        </tr>
                    @endforeach

                    </tbody>

                </table>
            </div>

        </div>

    </div>
@endsection
