@extends('layouts.app')

@section('title')
    {{'- Приход материала'}}
@endsection

@section('content')
    <div class="container">


        <div class="">
            <div class="d-flex flex-row mb-3 align-items-center gap-3">
                <p class="fw-bold m-0 fs-2">Новый приход</p>
            </div>
            <form method="post" action="{{route('director.receipt.store')}}">
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
                            <input required type="number" name="quantity1" class="form-control">
                        </div>
                    </div>
                </div>
                <p id="new-btn" class="text-primary mt-2 link-primary link-underline">Добавить позицию</p>
                <input class="btn btn-success" type="submit" value="Провести приход"/>
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
                <input type="number" name="quantity${id}" class="form-control">
            </div>
        </div>`);
                id++
            })
        </script>

    </div>
@endsection
