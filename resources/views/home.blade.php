@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @role('operator')
                        <script>
                            window.location.href = "{{route('leads.index')}}"
                        </script>
                        @endrole

                        @role('manager')
                        <script>
                            window.location.href = "{{route('manager.leads')}}"
                        </script>
                        @endrole

                        @role('coordinator')
                        <script>
                            window.location.href = "{{route('coordinator.managers')}}"
                        </script>
                        @endrole

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
