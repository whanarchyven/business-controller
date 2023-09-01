@extends('layouts.app')

@section('title')
    {{'- Новая номенклатура'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавление номенклатуры</div>

                    <div class="card-body">
                        <form enctype="multipart/form-data" action="{{route('director.store.nomenclature')}}"
                              method="post">
                            @csrf
                            @method('post')
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex gap-5 align-items-center">
                                    <div class="w-50">
                                        <label for="name">Название</label>
                                        <input class="form-control" type="text" name="name"/>
                                    </div>
                                    <div class="w-50">
                                        <label for="unit">Единица измерения</label>
                                        <input class="form-control" type="text" name="unit"/>
                                    </div>
                                    <div class="w-50">
                                        <label for="price">Цена за ед. измерения</label>
                                        <input class="form-control" type="text" name="price"/>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    <input class="form-control btn btn-success w-50" value="Создать" type="submit"/>
                                    <div onclick="window.location.href='{{route('director.add.nomenclature')}}'"
                                         class="btn w-50 btn-secondary">Назад
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
