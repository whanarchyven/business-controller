@extends('layouts.app')

@section('title')
    {{'- Таблица ремонтов'}}
@endsection

@section('content')
    <div class="container">
        <p class="fw-bold fs-3">Транзакция №{{$transaction->id}}, от {{$transaction->created_at}}</p>
        <p class="fs-4 fw-bold">
            {{$transaction->type=='receipt'?'Приход: ':'Расход: '}} <span
                class="{{$transaction->type=='receipt'?'text-success':'text-danger'}}">{{$transaction->type=='receipt'?'+ '.$transaction->value:'- '.$transaction->value}}</span>,
            ответственный {{$transaction->user->name}}
        </p>
        <div class="w-100">

            @foreach($documents as $document)
                <a href="{{ URL::to('/documents') }}/{{$document}}"
                   class="w-100 border border-2 border-black d-flex"><img
                        class="w-100 object-fit-cover"
                        src="{{ URL::to('/documents') }}/{{$document}}"/></a>
            @endforeach

        </div>
    </div>
@endsection
