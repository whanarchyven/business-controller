@extends('layouts.app')

@section('title')
    {{'- Новая транзакция'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Новая транзакция</div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form enctype="multipart/form-data" action="{{route('director.transactions.store')}}"
                                  method="post">
                                @csrf
                                @method('post')
                                <div class="form-row row-gap-10">
                                    <label for="state">Статья транзакции</label>
                                    <select name="state" class="form-select">
                                        @foreach($states as $state)
                                            <option value="{{$state->code}}">{{$state->code}} {{$state->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="d-flex my-2 flex-column gap-1">
                                        <p class="m-0">Тип транзакции</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="receipt"
                                                   id="receipt" checked>
                                            <label class="form-check-label" for="receipt">
                                                Приход
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="expense"
                                                   id="expense">
                                            <label class="form-check-label" for="expense">
                                                Расход
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="value">Сумма транзакции</label>
                                        <input type="number" class="form-control" id='value' name="value">
                                    </div>
                                    <label for="documents">Документы</label>
                                    <input enctype="multipart/form-data"
                                           type="file"
                                           class="my-2 form-control"
                                           name="documents[]"
                                           placeholder="Документы" multiple>
                                    <div class="form-group my-2">
                                        <input type="submit" class="form-control bg-primary text-white fw-bold"
                                               value="Отправить">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
@endsection
