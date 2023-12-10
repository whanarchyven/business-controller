@extends('layouts.app')

@section('title')
    {{'- Приём заявки'}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Заявка № {{$lead->id}} от {{$lead->created_at}}, {{$lead->city}}</div>

                    <div class="card-body">
                        @if($lead->check>0)
                            <form enctype="multipart/form-data" action="{{route('director.close.lead',$lead->id)}}"
                                  method="post">
                                @csrf
                                @method('patch')
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-5 align-items-center">
                                        <div class="w-50">
                                            <label for="issued">Чек встречи</label>
                                            <input required class="form-control" type="number" name="issued"
                                                   value="{{$lead->check}}"/>
                                        </div>
                                        <div class="w-50">
                                            <label for="check">Предоплата</label>
                                            <input required class="form-control" type="number" name="avance"/>
                                        </div>
                                    </div>
                                    <label>Дата ремонта</label>
                                    <input required type="date" class="form-control"
                                           id='repair_date' name="repair_date" list="repair_date">
                                    <label for="manager_id">Менеджер</label>
                                    <select name="manager_id" class="form-select">
                                        @foreach($managers as $manager)
                                            <option
                                                value="{{$manager->id}}" {{$manager->id==$lead->manager_id?'selected':''}}>{{$manager->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="documents">Документы</label>
                                    <input required enctype="multipart/form-data" type="file"
                                           class="my-2 form-control"
                                           name="documents[]"
                                           placeholder="Документы" multiple>
                                    <div class="d-flex gap-3 align-items-center">
                                        <input class="form-control btn btn-success w-50" type="submit"/>
                                        <div onclick="window.location.href='{{route('director.daily')}}'"
                                             class="btn w-50 btn-secondary">Назад
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form enctype="multipart/form-data"
                                  action="{{route('director.leads.close.null',$lead->id)}}"
                                  method="post">
                                @csrf
                                @method('patch')
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex gap-5 align-items-center">
                                        <div class="w-50">
                                            <label for="issued">Чек встречи</label>
                                            <input required class="form-control" type="number" name="issued"
                                                   value="{{$lead->check}}"/>
                                        </div>
                                    </div>
                                    <label for="manager_id">Менеджер</label>
                                    <select name="manager_id" class="form-select">
                                        @foreach($managers as $manager)
                                            <option
                                                value="{{$manager->id}}" {{$manager->id==$lead->manager_id?'selected':''}}>{{$manager->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="d-flex gap-3 align-items-center">
                                        <input class="form-control btn btn-success w-50" value="Закрыть нулевую"
                                               type="submit"/>
                                        <div onclick="window.location.href='{{route('director.daily')}}'"
                                             class="btn w-50 btn-secondary">Назад
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
@endsection
