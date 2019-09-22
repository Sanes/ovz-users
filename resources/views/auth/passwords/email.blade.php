@extends('layouts.base')

@section('title')
<title>{{ __('Reset Password') }}</title>
@endsection
@section('content')
<div class="uk-flex uk-flex-center">
    <div class="uk-card uk-card-default uk-box-shadow-large uk-width-xlarge uk-margin-bottom">
        <div class="uk-card-body">
            <h3 class="uk-heading-divider">{{ __('Reset Password') }}</h3>
            @if (session('status'))
                <div class="uk-alert uk-alert-primary uk-margin-bottom">
                    {{ session('status') }}
                </div>
            @endif
            <form action="{{ route('password.email') }}" method="POST" class="uk-form-horizontal">
                @csrf
                <div class="uk-margin">
                    <label class="uk-form-label" for="email">{{ __('E-Mail Address') }}</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus="">
                        @error('email')
                        <span class="uk-text-danger uk-text-small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="uk-margin">
                    <div class="uk-form-controls">
                        <button type="submit" class="uk-button uk-button-primary">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </div>
                @if (Route::has('register'))
                <div class="uk-margin">
                    <div class="uk-form-controls">
                        <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    </div>
                </div>
                @endif
                <div class="uk-margin">
                    <div class="uk-form-controls">
                        <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    </div>
                </div>
            </form>
        </div>    
    </div>    
</div>

@endsection