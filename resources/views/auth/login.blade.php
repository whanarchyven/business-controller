@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="row mb-3">
                        {{--                        <label for="email"--}}
                        {{--                               class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>--}}

                        <div class="">
                            <input placeholder="Логин" id="email" type="text"
                                   class="form-control" name="email"
                                   value="{{ old('email') }}" required autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        {{--                        <label for="password"--}}
                        {{--                               class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>--}}

                        <div class="">
                            <input placeholder="Пароль" id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                   required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>


                    <div class="row mb-0">
                        <div class="">
                            <button type="submit" class="btn w-100 btn-primary">
                                {{ __('Войти') }}
                            </button>

                            {{--                                @if (Route::has('password.request'))--}}
                            {{--                                    <a class="btn btn-link" href="{{ route('password.request') }}">--}}
                            {{--                                        {{ __('Forgot Your Password?') }}--}}
                            {{--                                    </a>--}}
                            {{--                                @endif--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
