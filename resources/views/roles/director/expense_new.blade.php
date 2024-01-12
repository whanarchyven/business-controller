@extends('layouts.app')

@section('title')
    {{'- Выдача материала'}}
@endsection

@section('content')
    <div class="container">


        <div class="">
            <div class="d-flex flex-column mb-3  gap-3">
                <p class="fw-bold m-0 fs-2">Расход материала, Ремонт №{{$repair->id}}</p>
                <p class="fs-6">от {{$repair->repair_date}},
                    мастер: {{$repair->master?$repair->master->name:'Не назначено'}}</p>
            </div>
            <form method="post" action="{{route('director.expense.store',$repair)}}">
                @csrf
                @method('post')
                <div id="new-container" class="d-flex w-100 flex-column gap-2">
                    <div class="d-flex w-100 flex-row gap-3">
                        <div class="w-25">
                            <label for="nomenclature1">Номенклатура</label>
                            <select class="form-select" name="nomenclature1">
                                @foreach($nomenclature as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-25">
                            <label for="quantity1">Количество</label>
                            <input step="0.01" required type="number" name="quantity1" class="form-control">
                        </div>
                    </div>
                </div>
                <p id="new-btn" class="text-primary mt-2 link-primary link-underline">Добавить позицию</p>
                <input class="btn btn-success" type="submit" value="Провести расход"/>
            </form>
        </div>

        <script>
            let id = 2;
            document.getElementById('new-btn').addEventListener('click', () => {
                document.querySelector("#new-container").insertAdjacentHTML('beforeEnd', `<div class="d-flex flex-row gap-3">
                    <div class="w-25">
                        <label for="nomenclature${id}">Номенклатура</label>
                        <select class="form-select" name="nomenclature${id}">
                            @foreach($nomenclature as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                </select>
            </div>
            <div class="w-25">
                <label for="quantity${id}">Количество</label>
                <input step="0.01" type="number" name="quantity${id}" class="form-control">
            </div>
        </div>`);
                id++
            })
        </script>

    </div>
@endsection
